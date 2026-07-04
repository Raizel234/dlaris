<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('menu');
        return [
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255|unique:menus,nama,' . $id,
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_tersedia' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_new' => 'boolean',
        ];
    }
}
