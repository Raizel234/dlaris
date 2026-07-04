<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('kategori');
        return [
            'nama' => 'required|string|max:255|unique:kategoris,nama,' . $id,
            'deskripsi' => 'nullable|string',
            'ikon' => 'nullable|string|max:100',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ];
    }
}
