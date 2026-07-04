<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadFotoMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
