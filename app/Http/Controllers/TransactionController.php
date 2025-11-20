<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\InstallmentPayment;
use App\Models\ProductHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $keyword = $request->input('filters.keyword');
        $transactions = Transaction::query()
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            // ->join('products as product', 'transaction_details.product_id', '=', 'product.id')
            // ->join('variants as variant', 'transaction_details.variant_id', '=', 'variant.id')
            ->leftJoin('product_histories as product_history', 'transaction_details.id', "=", 'product_history.transaction_detail_id')
            ->where(function ($q) use ($keyword) {
                $q->whereRaw("CONCAT_WS(' ', transactions.invoice_code) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', transactions.buyer_name) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', product_history.name, product_history.merk) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("CONCAT_WS(' ', product_history.merk) LIKE ?", ["%{$keyword}%"]);
            })
            ->when($request->filled('filters.date'), fn($q) => $q->whereDate('transaction_date', $request->input('filters.date')))
            ->when($request->filled('filters.sort_by'), function ($q) use ($request) {
                $q->orderBy($request->input('filters.sort_by'), $request->input('filters.sort_type'));
            }, function ($q) {
                $q->latest();
            })
            ->groupBy('transactions.id')
            ->select('transactions.*')
            ->with('transaction_details', 'transaction_details.product_history')
            ->paginate(5)->withQueryString();
        // $transactions = vsprintf(str_replace('?', "'%s'", $transactions->toSql()), $transactions->getBindings());
        // dd($transactions);

        return inertia('transactions/Index', [
            'transactions' => TransactionResource::collection($transactions),
            'search' => $request->filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('transactions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request)
    {
        $request->validated();
        $is_done = true;
        if ($request->is_installment) {
            if ($request->payment_nominal >= $request->grand_total) {
                throw ValidationException::withMessages([
                    'is_installment' => 'nominal pembayaran mencukupi, tidak dapat memilih opsi cicilan',
                ]);
            }
            $is_done = false;
        }

        DB::beginTransaction();
        try {
            DB::table('transactions')->sharedLock()->get();
            //insert into transactions table
            $user = $request->user();
            $invoice_code = Transaction::GenerateInvoiceCode();
            $transaction = Transaction::create([
                'invoice_code' => $invoice_code,
                'user_id' => $user->id ?? null,
                'buyer_id' => $request->buyer_id,
                'buyer_name' => $request->buyer_name,
                'transaction_date' => $request->transaction_date,
                'total_price' => $request->total_price,
                'additional_cost' => $request->additional_cost,
                'is_installment' => $request->is_installment,
                'discount' => $request->discount,
                'grand_total' => $request->grand_total,
                'is_done' => $is_done,
            ]);

            //insert into transaction_details table
            foreach ($request->transaction_detail as $detail) {
                // insert into transaction detail table
                $transaction_detail = TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $detail['product_id'],
                    'variant_id' => $detail['variant_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'sub_total' => $detail['sub_total'],
                ]);

                // insert into product history table
                ProductHistory::create([
                    'transaction_detail_id' => $transaction_detail->id,
                    'name' => $detail['product_name'],
                    'merk' => $detail['variant_merk'],
                    'unit' => $detail['variant_unit'],
                    'dimension' => $detail['variant_dimension'],
                    'color' => $detail['variant_color'],
                    'price' => $detail['price']
                ]);

                //update stock in variants table
                $variant = DB::table('variants')->where('id', $detail['variant_id'])->lockForUpdate()->first();
                if ($variant->stock < $detail['quantity']) {
                    DB::rollBack();
                    throw ValidationException::withMessages([
                        'is_installment' => 'stok varian ' . $variant->merk . ' tidak mencukupi',
                    ]);
                } else {
                    DB::table('variants')->where('id', $detail['variant_id'])->update([
                        'stock' => $variant->stock - $detail['quantity'],
                    ]);
                }
            }

            if ($request->is_installment) {
                // Logic for installment payments can be added here
                InstallmentPayment::create([
                    'transaction_id' => $transaction->id,
                    'sequence' => 1,
                    'amount' => $request->payment_nominal,
                    'payment_date' => $request->transaction_date,
                ]);
            }
            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            // throw new \Exception($e->getMessage());
            // return redirect()->back()->with('message', 'Terjadi kesalahan saat menyimpan data');
            // Menggunakan ValidationException untuk mengirim pesan error ke form Inertia dengan meminjam key 'is_installment'
            throw ValidationException::withMessages([
                'is_installment' => 'terjadi kesalahan saat menyimpan data : ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::with('transaction_details', 'transaction_details.product_history', 'installment_payments')->findOrFail($id);
        return inertia('transactions/Detail', [
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
