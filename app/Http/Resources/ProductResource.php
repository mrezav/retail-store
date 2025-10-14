<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'image_path' => $this->image_path ? asset('storage/' . $this->image_path) : "",
            'category' => new CategoryResource($this->whenLoaded('category')),
            'category_id' => $this->category_id,
            'variants' => VariantResource::collection($this->whenLoaded('variants')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'price_min' => $this->whenLoaded('variants', $this->variants()->min('price')),
            'price_max' => $this->whenLoaded('variants', $this->variants()->max('price')),
            // 'variant_images' => VariantImageResource::collection($this->whenLoaded('variant_images')),
        ];
    }
}
