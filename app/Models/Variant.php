<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variant extends Model
{

    protected $table = 'variants';

    protected $fillable = [
        'product_id',
        'merk',
        'color',
        'dimension',
        'image_path',
        'stock',
        'unit',
        'price',
        'description',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // public function variant_images(): HasMany
    // {
    //     return $this->hasMany(VariantImage::class);
    // }
}
