<?php

namespace App\Modules\Payment\Services;

use App\Enums\SaleStatus;
use App\Modules\Payment\DTOs\CreatePaymentDTO;
use App\Modules\Payment\DTOs\UpdatePaymentDTO;
use App\Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;
use App\Modules\Sale\Repositories\Contracts\SaleRepositoryInterface;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PaymentService extends BaseService
{
    public function __construct(
        protected PaymentRepositoryInterface $payments,
        protected SaleRepositoryInterface $sales
    ) {
    }

    public function paginate(int $perPage = 10)
    {
        return $this->payments->paginate($perPage);
    }

    public function find(string $id)
    {
        return $this->payments->find($id);
    }

    public function store(CreatePaymentDTO $data)
    {
        return $this->transaction(function () use ($data) {
            $sale = $this->sales->find($data->sale_id);

            if (!$sale) {
                return null;
            }

            $payment = $this->payments->create([
                'code' => $this->generatePaymentCode($data->date),
                'sale_id' => $data->sale_id,
                'amount' => $sale->total_amount,
                'date' => Carbon::parse($data->date),
                'payment_method' => $data->payment_method,
            ]);

            $this->sales->update($sale->id, [
                'status' => SaleStatus::PAID->value,
            ]);
            $this->audit('create', $payment, [], $payment->toArray());

            return $payment;
        });
    }

    public function update(string $id, UpdatePaymentDTO $data)
    {
        return $this->transaction(function () use ($id, $data) {
            $payment = $this->payments->find($id);

            if (!$payment) {
                return null;
            }

            $oldSaleId = $payment->sale_id;
            $payload = array_filter($data->toArray(), static fn ($value) => $value !== null);
            $updatePayload = [];

            if (isset($payload['date'])) {
                $updatePayload['code'] = $this->generatePaymentCode($payload['date']);
                $updatePayload['date'] = Carbon::parse($payload['date']);
            }

            if (isset($payload['payment_method'])) {
                $updatePayload['payment_method'] = $payload['payment_method'];
            }

            if ($updatePayload === []) {
                return $payment;
            }

            $updated = $this->payments->update($id, $updatePayload);

            $this->syncSaleStatus($oldSaleId);
            if ($updated) {
                $this->audit('update', $updated, $payment->toArray(), $updated->toArray());
            }
            return $updated;
        });
    }

    public function delete(string $id)
    {
        return $this->transaction(function () use ($id) {
            $payment = $this->payments->find($id);

            if (!$payment) {
                return null;
            }

            $saleId = $payment->sale_id;
            $deleted = $this->payments->delete($id);

            $this->syncSaleStatus($saleId);
            if ($deleted) {
                $this->audit('delete', $payment, $payment->toArray(), []);
            }

            return $deleted;
        });
    }

    private function syncSaleStatus(string $saleId): void
    {
        $sale = $this->sales->find($saleId);

        if (!$sale) {
            return;
        }

        $hasPayment = $this->payments->existsForSale($saleId);
        $updated = $this->sales->update($saleId, [
            'status' => $hasPayment ? SaleStatus::PAID->value : SaleStatus::UNPAID->value,
        ]);

        if ($updated) {
            $this->audit('update', $updated, $sale->toArray(), $updated->toArray());
        }
    }

    private function generatePaymentCode(string $paymentDate): string {
        return 'PAY-'.Carbon::parse($paymentDate)->format('Ymd').'-'.Str::upper(Str::random(6));
    }
}
