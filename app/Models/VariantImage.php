<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantImage extends Model
{
    protected $fillable = ['variant_id', 'path'];

    public function variant():BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
