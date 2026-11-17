<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HasilPenyaluranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('catatHasil', $this->route('penyaluran'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:diterima,tidak_diterima'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
