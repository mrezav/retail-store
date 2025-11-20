<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    protected $fillable = ['transaction_detail_id','name', 'merk','unit','dimension','color','price'];

    public function product_detail():BelongsTo
    {
        return $this->belongsTo(TransactionDetail::class);
    }
}
