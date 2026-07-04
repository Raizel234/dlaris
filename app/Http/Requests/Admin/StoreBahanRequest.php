<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100|unique:bahans,nama',
            'satuan' => 'required|string|max:20',
            'stok' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'menus' => 'nullable|array',
            'menus.*.id' => 'exists:menus,id',
            'menus.*.jumlah' => 'nullable|numeric|min:0',
        ];
    }
}
