<?php

namespace App\Modules\Sale\Services;

use App\Enums\SaleStatus;
use App\Models\Product;
use App\Modules\Sale\DTOs\CreateSaleDTO;
use App\Modules\Sale\DTOs\UpdateSaleDTO;
use App\Modules\SaleDetail\Repositories\Contracts\SaleDetailRepositoryInterface;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Sale\Repositories\Contracts\SaleRepositoryInterface;
use App\Modules\Shared\Support\CsvHelper;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

class SaleService extends BaseService
{
    public function __construct(
        protected SaleRepositoryInterface $sales,
        protected SaleDetailRepositoryInterface $saleDetails,
        protected ProductRepositoryInterface $products
    ) {
    }

    public function paginate(int $perPage = 10, array $filters = [])
    {
        return $this->sales->paginate($perPage, $filters);
    }

    public function find(string $id)
    {
        return $this->sales->find($id);
    }

    public function store(CreateSaleDTO $data)
    {
        return $this->transaction(function () use ($data) {
            $sale = $this->sales->create([
                'code' => $this->generateSaleCode($data->date),
                'date' => Carbon::parse($data->date)->startOfDay(),
                'total_qty' => 0,
                'total_amount' => 0,
                'status' => SaleStatus::UNPAID->value,
            ]);
            $this->audit('create', $sale, [], $sale->toArray());

            $totalQty = 0;
            $totalAmount = 0;

            foreach ($data->products as $row) {
                $product = $this->products->find($row['product_id']);

                if (!$product) {
                    throw new \RuntimeException('Product not found');
                }

                /** @var Product $productModel */
                $productModel = $product;
                $qty = (float) $row['qty'];
                $this->assertStockAvailable($productModel, $qty);
                $price = (float) $productModel->price;
                $lineTotal = $qty * $price;

                $this->saleDetails->create([
                    'sale_id' => $sale->id,
                    'product_id' => $productModel->id,
                    'qty' => $qty,
                    'price' => $price,
                    'total_price' => $lineTotal,
                ]);

                $this->products->update($productModel->id, [
                    'stock' => (float) $productModel->stock - $qty,
                ]);

                $totalQty += $qty;
                $totalAmount += $lineTotal;
            }

            $updatedSale = $this->sales->update($sale->id, [
                'total_qty' => $totalQty,
                'total_amount' => $totalAmount,
            ]);

            if ($updatedSale) {
                $this->audit('create', $updatedSale, [], $updatedSale->toArray());
            }

            return $this->sales->find($sale->id);
        });
    }

    public function update(string $id, UpdateSaleDTO $data)
    {
        return $this->transaction(function () use ($id, $data) {
            $sale = $this->sales->find($id);

            if (!$sale) {
                return null;
            }

            $payload = array_filter($data->toArray(), static fn ($value) => $value !== null);
            $updated = $this->sales->update($id, $payload);

            if ($updated) {
                $this->audit('update', $updated, $sale->toArray(), $updated->toArray());
            }

            return $updated;
        });
    }

    public function delete(string $id)
    {
        return $this->transaction(function () use ($id) {
            $sale = $this->sales->find($id);
            $details = $this->saleDetails->findBySaleId($id);

            if (!$sale) {
                return null;
            }

            foreach ($details as $detail) {
                /** @var Product $product */
                $product = $this->products->find($detail->product_id);

                if ($product) {
                    $this->products->update($product->id, [
                        'stock' => (float) $product->stock + (float) $detail->qty,
                    ]);
                }
            }

            $deleted = $this->sales->delete($id);

            if ($deleted) {
                $this->audit('delete', $sale, $sale->toArray(), []);
            }

            return $deleted;
        });
    }

    public function exportCsv(array $filters = [])
    {
        $sales = $this->sales->all();

        $rows = $sales->map(function ($sale) {
            return [
                $sale->code,
                optional($sale->date)?->format('Y-m-d'),
                (string) $sale->total_qty,
                (string) $sale->total_amount,
                $sale->status instanceof SaleStatus ? $sale->status->value : (string) $sale->status,
                optional($sale->created_at)?->format('Y-m-d H:i:s'),
            ];
        })->all();

        return CsvHelper::download(
            'sales-history-'.now()->format('Ymd-His').'.csv',
            ['code', 'date', 'total_qty', 'total_amount', 'status', 'created_at'],
            $rows
        );
    }

    private function generateSaleCode(string $saleDate): string
    {
        return 'SL-'.Carbon::parse($saleDate)->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    private function assertStockAvailable(Product $product, float $qty): void
    {
        if ((float) $product->stock < $qty) {
            throw new RuntimeException("Insufficient stock for product {$product->name}");
        }
    }
}
