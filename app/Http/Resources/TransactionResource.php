<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_code' => $this->invoice_code,
            'id' => $this->id,
            'user_id' => $this->user_id,
            'buyer_id' => $this->buyer_id,
            'buyer_name' => $this->buyer_name,
            'transaction_date' => Carbon::parse($this->transaction_date)->format('d M Y'),
            'total_price' => $this->total_price,
            'additional_cost' => $this->additional_cost,
            'grand_total' => $this->grand_total,
            'is_installment' => $this->is_installment,
            'is_done' => $this->is_done,
            'transaction_details' => TransactionDetailResource::collection($this->whenLoaded('transaction_details')),
            'installment_payments' => InstallmentPaymentResource::collection($this->whenLoaded('installment_payments')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
