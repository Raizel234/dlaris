<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255|unique:ruangans,nama',
            'kapasitas' => 'required|integer|min:1',
            'tarif_per_jam' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:tersedia,digunakan,maintenance',
        ];
    }
}
