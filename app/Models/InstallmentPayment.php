<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class InstallmentPayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'sequence',
        'amount',
        'payment_date',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public static function getLastSequence($transaction_id) 
    {
        return self::where('transaction_id', $transaction_id)->max('sequence');
    }
}
