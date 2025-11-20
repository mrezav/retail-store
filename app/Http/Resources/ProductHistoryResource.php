<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductHistoryResource extends JsonResource
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
            'transaction_detail_id' => $this->transaction_detail_id,
            'name' => $this->name, 
            'merk' => $this->merk, 
            'unit' => $this->unit, 
            'dimension' => $this->dimension, 
            'color' => $this->color, 
            'price' => $this->price,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
