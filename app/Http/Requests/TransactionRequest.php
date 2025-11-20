<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'buyer_name' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'grand_total' => 'numeric|min:0',
            'transaction_detail' => 'required|array|min:1',
            'transaction_detail.*.variant_id' => 'required|exists:variants,id',
            'transaction_detail.*.quantity' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'buyer_name.required' => 'nama pembeli masih kosong',
            'transaction_date.required' => 'tanggal transaksi masih kosong',
            'transaction_date.date' => 'format tanggal salah',
            'grand_total.numeric' => 'format total harga salah',
            'grand_total.min' => 'total harga minimal 0',
            'transaction_detail.required' => 'detail transaksi masih kosong',
            'transaction_detail.array' => 'format detail transaksi salah',
            'transaction_detail.min' => 'detail transaksi minimal 1 item',
            'transaction_detail.*.variant_id.required' => 'varian belum dipilih',
            'transaction_detail.*.variant_id.exists' => 'varian tidak terdaftar',
            'transaction_detail.*.quantity.required' => 'jumlah masih kosong',
            'transaction_detail.*.quantity.numeric' => 'format jumlah salah',
            'transaction_detail.*.quantity.min' => 'jumlah minimal 1',
        ];
    }
}
