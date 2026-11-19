<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusTindakLanjut;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TindakLanjutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tindakLanjut', $this->route('penyaluran'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(StatusTindakLanjut::class)],
            'catatan' => ['nullable', 'required_if:status,'.StatusTindakLanjut::Berhenti->value, 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return ['catatan.required_if' => 'Tulis alasan berhenti bekerja (mis. kontrak habis, gaji tidak dibayar, pindah).'];
    }
}
