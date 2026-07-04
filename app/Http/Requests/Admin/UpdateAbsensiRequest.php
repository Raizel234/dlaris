<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpha,cuti',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
