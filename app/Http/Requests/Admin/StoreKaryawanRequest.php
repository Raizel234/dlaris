<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'jabatan' => 'required|string|in:admin,kasir,karyawan',
            'nomor_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
