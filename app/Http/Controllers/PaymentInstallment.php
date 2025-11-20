<?php

namespace App\Http\Controllers;

use App\Models\InstallmentPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentInstallment extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // dd($request->transaction_id);

        $id = $request->transaction_id;
        DB::beginTransaction();
        try {
            $last_sequence = InstallmentPayment::getLastSequence($id);
            $installment = new InstallmentPayment();
            $installment->transaction_id = $id;
            $installment->sequence = $last_sequence + 1;
            $installment->amount = $request->nominal_payment;
            $installment->payment_date = $request->payment_date;
            $installment->save();

            if($request->is_done){
                Transaction::where('id', $id)->update([
                    'is_done' => true
                ]);
            }

            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        return redirect()->route('transactions.show', $id);
    }
}
