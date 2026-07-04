<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('ruangan');
        return [
            'nama' => 'required|string|max:255|unique:ruangans,nama,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'tarif_per_jam' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:tersedia,digunakan,maintenance',
        ];
    }
}
