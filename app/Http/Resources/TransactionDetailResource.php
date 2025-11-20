<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'variant_id' => $this->variant_id,
            'variant' => new VariantResource($this->whenLoaded('variant')),
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_history' => new ProductHistoryResource($this->whenLoaded('product_history')),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'sub_total' => $this->sub_total,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDatetimeString(),
        ];
    }
}
