<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_code',
        'user_id',
        'buyer_id',
        'buyer_name',
        'transaction_date',
        'total_price',
        'additional_cost',
        'discount',
        'is_installment',
        'due_date',
        'grand_total',
        'is_done',
    ];

    public function transaction_details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function installment_payments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function GenerateInvoiceCode(): string
    {
        $prefix = 'INV';
        $datePart = date('dmY');

        // cek transaksi terakhir untuk mendapatkan nomor urut terakhir
        $latestTransaction = self::whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();
        if($latestTransaction){
            $lastCounter = (int) substr($latestTransaction->invoice_code, -3);
            $newCounter = $lastCounter + 1;
        }else{
            $newCounter = 1;
        }
        $counterPart = str_pad($newCounter, 3, '0', STR_PAD_LEFT);
        return $prefix .'/'. $datePart .'/'. $counterPart;
    }
}
