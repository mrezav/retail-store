<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
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
            'merk' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array 
    {
        return [
            'product_id.required' => 'produk belum dipilih',
            'product_id.exists' => 'produk tida terdaftar',
            'unit.required' => 'merk masih kosong',
            'merk.required' => 'merk masih kosong',
            'stock.required' => 'stok masih kosong',
            'stock.min' => 'stok minimal 1',
            'price.required' => 'harga masih kosong',
            'price.min' => 'harga minimal 1',
            'image.mimes' => 'format gambar salah',
            'image.image' => 'file yang dipilih bukan gambar',
        ];
    }
}
