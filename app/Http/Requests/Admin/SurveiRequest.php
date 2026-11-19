<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Keputusan survei (instansi / lowongan): ACC atau tolak dengan alasan. */
class SurveiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek Policy di controller
    }

    public function rules(): array
    {
        return [
            'keputusan' => ['required', 'in:terima,tolak'],
            'catatan_survei' => ['nullable', 'string', 'max:2000'],
            'alasan_tolak' => ['required_if:keputusan,tolak', 'nullable', 'string', 'max:2000'],
            'legalitas_diperiksa' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return ['alasan_tolak.required_if' => 'Alasan penolakan wajib diisi.'];
    }

    public function diterima(): bool
    {
        return $this->input('keputusan') === 'terima';
    }
}
