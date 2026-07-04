<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
