<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DaftarPelatihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('daftar-pelatihan');
    }

    public function rules(): array
    {
        return [
            'alasan' => ['required', 'string', 'max:2000'],
            'pengalaman_bidang' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
