<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5000',
            'variants' => 'required',
        ];
    }

    public function messages(): array 
    {
        return [
            'name.required' => 'nama barang masih kosong!',
            'name.max' => 'nama barang lebih dari 255 karakter',
            'category_id.required' => 'kategori belum dipilih',
            'category_id.exists' => 'error exists category',
            'image.mimes' => 'format gambar salah',
            'image.max' => 'ukuran gambar maksimal 5mb',
            'image.image' => 'file yang dipilih bukan gambar',
            'variants.required' => 'silahkan tambah variant',
        ];
    }
}
