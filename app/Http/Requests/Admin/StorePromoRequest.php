<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:50|unique:promos,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:persen,nominal,buy_get,free_ongkir',
            'nilai' => 'required|numeric|min:0',
            'min_belanja' => 'nullable|numeric|min:0',
            'maks_diskon' => 'nullable|numeric|min:0',
            'kuota' => 'nullable|integer|min:1',
            'berlaku_mulai' => 'nullable|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_mulai',
            'is_active' => 'boolean',
        ];
    }
}
