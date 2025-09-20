<?php

namespace App\Http\Resources;

use App\Models\VariantImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
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
            'product_id' => $this->product_id,
            'merk' => $this->merk,
            'color' => $this->color,
            'size' => $this->size,
            'price' => $this->price,
            'stock' => $this->stock,
            'dimension' => $this->dimension,
            'unit' => $this->unit,
            'product' => $this->whenLoaded('product'),
            'image_path' => $this->image_path ? asset('storage/' . $this->image_path) : asset($this->product->image_path)
            // 'variant_images' => VariantImageResource::collection($this->whenLoaded('variant_images'))
        ];
    }
}
