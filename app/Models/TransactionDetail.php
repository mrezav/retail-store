<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
        'sub_total',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_history():HasOne
    {
        return $this->hasOne(ProductHistory::class);
    }
}
