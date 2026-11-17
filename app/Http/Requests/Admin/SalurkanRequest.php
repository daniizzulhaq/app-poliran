<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SalurkanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek Policy lowongan & pendaftar di controller
    }

    public function rules(): array
    {
        return ['pencari_kerja_id' => ['required', 'integer', 'exists:pencari_kerja,id']];
    }
}
