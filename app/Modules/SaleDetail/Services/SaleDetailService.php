<?php

namespace App\Modules\SaleDetail\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Modules\SaleDetail\DTOs\CreateSaleDetailDTO;
use App\Modules\SaleDetail\DTOs\UpdateSaleDetailDTO;
use App\Modules\SaleDetail\Repositories\Contracts\SaleDetailRepositoryInterface;
use App\Modules\Sale\Repositories\Contracts\SaleRepositoryInterface;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Shared\Services\BaseService;
use RuntimeException;

class SaleDetailService extends BaseService
{
    public function __construct(
        protected SaleDetailRepositoryInterface $saleDetails,
        protected SaleRepositoryInterface $sales,
        protected ProductRepositoryInterface $products
    ) {
    }

    public function paginate(int $perPage = 10)
    {
        return $this->saleDetails->paginate($perPage);
    }

    public function find(string $id)
    {
        return $this->saleDetails->find($id);
    }

    public function store(CreateSaleDetailDTO $data)
    {
        return $this->transaction(function () use ($data) {
            $sale = $this->sales->find($data->sale_id);
            $product = $this->products->find($data->product_id);

            if (!$sale || !$product) {
                return null;
            }

            $existing = $this->saleDetails->findBySaleIdAndProductId($data->sale_id, $data->product_id);

            if ($existing) {
                return null;
            }

            /** @var Product $productModel */
            $productModel = $product;
            $qty = (float) $data->qty;
            $this->assertStockAvailable($productModel, $qty);
            $totalPrice = (float) $productModel->price * $qty;

            $detail = $this->saleDetails->create([
                'sale_id' => $data->sale_id,
                'product_id' => $data->product_id,
                'qty' => $qty,
                'price' => $productModel->price,
                'total_price' => $totalPrice,
            ]);

            $this->products->update($productModel->id, [
                'stock' => (float) $productModel->stock - $qty,
            ]);

            $this->syncSaleTotals($data->sale_id);
            $this->audit('create', $detail, [], $detail->toArray());

            return $detail;
        });
    }

    public function update(string $id, UpdateSaleDetailDTO $data)
    {
        return $this->transaction(function () use ($id, $data) {
            $detail = $this->saleDetails->find($id);

            if (!$detail) {
                return null;
            }

            $oldSaleId = $detail->sale_id;
            $saleId = $data->sale_id ?? $detail->sale_id;
            $productId = $data->product_id ?? $detail->product_id;
            $previousQty = (float) $detail->qty;

            $sale = $this->sales->find($saleId);
            $product = $this->products->find($productId);

            if (!$sale || !$product) {
                return null;
            }

            $duplicate = $this->saleDetails->findBySaleIdAndProductId($saleId, $productId);
            if ($duplicate && $duplicate->id !== $detail->id) {
                return null;
            }

            /** @var Product $productModel */
            $productModel = $product;
            $qty = (float) ($data->qty ?? $detail->qty);

            if ($detail->product_id === $productId) {
                $available = (float) $productModel->stock + $previousQty;
                if ($available < $qty) {
                    throw new RuntimeException("Insufficient stock for product {$productModel->name}");
                }
                $newStock = $available - $qty;
            } else {
                $this->restoreStock($detail->product_id, $previousQty);
                $this->assertStockAvailable($productModel, $qty);
                $newStock = (float) $productModel->stock - $qty;
            }

            $payload = [
                'sale_id' => $saleId,
                'product_id' => $productId,
                'qty' => $qty,
                'price' => $productModel->price,
                'total_price' => (float) $productModel->price * $qty,
            ];

            $updated = $this->saleDetails->update($id, $payload);
            $this->products->update($productModel->id, [
                'stock' => $newStock,
            ]);
            $this->syncSaleTotals($saleId);
            if ($oldSaleId !== $saleId) {
                $this->syncSaleTotals($oldSaleId);
            }
            if ($updated) {
                $this->audit('update', $updated, $detail->toArray(), $updated->toArray());
            }

            return $updated;
        });
    }

    public function delete(string $id)
    {
        return $this->transaction(function () use ($id) {
            $detail = $this->saleDetails->find($id);

            if (!$detail) {
                return null;
            }

            $saleId = $detail->sale_id;
            $deleted = $this->saleDetails->delete($id);
            $this->restoreStock($detail->product_id, (float) $detail->qty);
            $this->syncSaleTotals($saleId);
            if ($deleted) {
                $this->audit('delete', $detail, $detail->toArray(), []);
            }

            return $deleted;
        });
    }

    private function syncSaleTotals(string $saleId): void
    {
        $sale = $this->sales->find($saleId);
        $summary = $this->saleDetails->sumBySaleId($saleId);

        if (!$sale) {
            return;
        }

        $updated = $this->sales->update($saleId, [
            'total_qty' => $summary['total_qty'],
            'total_amount' => $summary['total_amount'],
        ]);

        if ($updated) {
            $this->audit('update', $updated, $sale->toArray(), $updated->toArray());
        }
    }

    private function restoreStock(string $productId, float $qty): void
    {
        $product = $this->products->find($productId);

        if (! $product) {
            return;
        }

        $this->products->update($productId, [
            'stock' => (float) $product->stock + $qty,
        ]);
    }

    private function assertStockAvailable(Product $product, float $qty): void
    {
        if ((float) $product->stock < $qty) {
            throw new RuntimeException("Insufficient stock for product {$product->name}");
        }
    }
}
