<?php

namespace App\Models;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'slug', 'image_path', 'description', 'is_active','category_id'
    ];

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants() :HasMany
    {
        return $this->hasMany(Variant::class);
    }

    // public function variant_images(): HasManyThrough
    // {
    //     return $this->hasManyThrough(VariantImage::class, Variant::class);
    // }
}
