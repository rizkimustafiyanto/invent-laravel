<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Shared\Support\CsvHelper;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService extends BaseService
{
    public function __construct(
        protected ProductRepositoryInterface $products
    ) {
    }

    public function paginate(int $perPage = 10, array $filters = [])
    {
        return $this->products->paginate($perPage, $filters);
    }

    public function find(string $id)
    {
        return $this->products->find($id);
    }

    public function store(CreateProductDTO $data, ?UploadedFile $image = null)
    {
        return $this->transaction(function () use ($data, $image) {
            if ($image) {
                $data->image_path = $image->store('products', 'public');
            }

            $payload = array_filter($data->toArray(), static fn ($value) => $value !== null);
            $product = $this->products->create($payload);
            $this->audit('create', $product, [], $product->toArray());

            return $product;
        });
    }

    public function update(string $id, UpdateProductDTO $data, ?UploadedFile $image = null)
    {
        return $this->transaction(function () use ($id, $data, $image) {
            $product = $this->products->find($id);

            if (!$product) {
                return null;
            }

            $payload = array_filter($data->toArray(), static fn ($value) => $value !== null);

            if ($image) {
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }

                $payload['image_path'] = $image->store('products', 'public');
            }

            $updated = $this->products->update($id, $payload);

            if ($updated) {
                $this->audit('update', $updated, $product->toArray(), $updated->toArray());
            }

            return $updated;
        });
    }

    public function delete(string $id)
    {
        return $this->transaction(function () use ($id) {
            $product = $this->products->find($id);

            if (!$product) {
                return null;
            }

            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $deleted = $this->products->delete($id);

            if ($deleted) {
                $this->audit('delete', $product, $product->toArray(), []);
            }

            return $deleted;
        });
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $products = $this->products->all();

        $rows = $products->map(fn ($product) => [
            $product->code,
            $product->name,
            optional($product->category)?->name,
            (string) $product->price,
            (string) $product->stock,
        ])->all();

        return CsvHelper::download(
            'products-export-'.now()->format('Ymd-His').'.csv',
            ['code', 'name', 'category', 'price', 'stock'],
            $rows
        );
    }

    public function templateCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return CsvHelper::download(
            'products-template.csv',
            ['code', 'name', 'category_id', 'price', 'stock'],
            [
                ['ITEM-001', 'Contoh Product', '', '15000', '0'],
            ]
        );
    }

    public function importCsv(string $path, int $createdBy): array
    {
        return $this->transaction(function () use ($path) {
            $rows = CsvHelper::read($path);
            $header = array_map(static fn ($value) => strtolower(trim((string) $value)), array_shift($rows) ?? []);

            $required = ['code', 'name', 'price'];
            foreach ($required as $column) {
                if (! in_array($column, $header, true)) {
                    return [
                        'created' => 0,
                        'updated' => 0,
                        'skipped' => count($rows),
                        'errors' => ["Missing column: {$column}"],
                    ];
                }
            }

            $map = array_flip($header);
            $created = 0;
            $updatedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $line = $index + 2;
                $code = $row[$map['code']] ?? null;
                $name = $row[$map['name']] ?? null;
                $price = $row[$map['price']] ?? null;
                if (! $code || ! $name || $price === null || $price === '') {
                    $errors[] = "Row {$line}: code, name, dan price wajib diisi.";
                    continue;
                }

                $payload = [
                    'code' => $code,
                    'name' => $name,
                    'price' => (float) $price,
                    'stock' => isset($map['stock']) ? (float) ($row[$map['stock']] ?? 0) : 0,
                    'category_id' => isset($map['category_id']) && ($row[$map['category_id']] ?? '') !== '' ? $row[$map['category_id']] : null,
                ];

                $existing = $this->products->findByCode($code);

                if ($existing) {
                    $updatedProduct = $this->products->update($existing->id, $payload);
                    if ($updatedProduct) {
                        $this->audit('update', $updatedProduct, $existing->toArray(), $updatedProduct->toArray());
                    }
                    $updatedCount++;
                } else {
                    $payload['created_by'] = null;
                    $createdProduct = $this->products->create($payload);
                    $this->audit('create', $createdProduct, [], $createdProduct->toArray());
                    $created++;
                }
            }

            return [
                'created' => $created,
                'updated' => $updatedCount,
                'skipped' => count($rows) - $created - $updatedCount,
                'errors' => $errors,
            ];
        });
    }
}
