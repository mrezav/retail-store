<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'merk',
        'color',
        'dimension',
        'image_path',
        'stock',
        'unit',
        'price',
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
