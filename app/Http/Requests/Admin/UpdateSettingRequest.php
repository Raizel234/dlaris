<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_toko' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'pajak' => 'nullable|numeric|min:0|max:100',
            'service_charge' => 'nullable|numeric|min:0|max:100',
            'ongkir' => 'nullable|numeric|min:0',
            'jam_buka' => 'nullable|string|max:10',
            'jam_tutup' => 'nullable|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'wa_api_key' => 'nullable|string|max:255',
            'wa_sender' => 'nullable|string|max:50',
            'wa_api_url' => 'nullable|string|max:255',
        ];
    }
}
