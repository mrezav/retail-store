<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

Carbon::setLocale('id');
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
            'product' => new ProductResource($this->whenLoaded('product')),
            'description' => $this->description,
            // 'image_path' => $this->image_path ? asset('storage/' . $this->image_path) : ($this->whenLoaded('product') ? ($this->product->image_path ? asset('storage/' . $this->product->image_path) : "") : ""),
            'image_path' => $this->image_path ? asset('storage/' . $this->image_path) : "",
            // 'variant_images' => VariantImageResource::collection($this->whenLoaded('variant_images'))
            'created_at' => Carbon::parse($this->created_at)->isoFormat('dddd, D MMMM Y HH:mm:ss'),
            'updated_at' => Carbon::parse($this->updated_at)->isoFormat('dddd, D MMMM Y HH:mm:ss'),
        ];
    }
}
