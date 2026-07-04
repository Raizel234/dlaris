<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => 'required|exists:menus,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
            'transaksi_id' => 'nullable|exists:transaksis,id',
        ];
    }
}
