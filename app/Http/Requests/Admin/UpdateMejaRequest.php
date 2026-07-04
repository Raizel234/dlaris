<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMejaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('meja');
        return [
            'nomor_meja' => 'required|string|max:50|unique:mejas,nomor_meja,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'area' => 'nullable|string|max:100',
            'status' => 'required|in:tersedia,terisi,reserved,dipakai,maintenance',
        ];
    }
}
