<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusPenyaluran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbahTahapPenyaluranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('ubahTahap', $this->route('penyaluran'));
    }

    protected function prepareForValidation(): void
    {
        $gaji = $this->input('gaji');
        $this->merge(['gaji' => $gaji === null || $gaji === '' ? null : preg_replace('/\D/', '', (string) $gaji)]);
    }

    public function rules(): array
    {
        $boleh = array_map(fn ($s) => $s->value, $this->route('penyaluran')->status->berikutnya());

        return [
            'status' => ['required', Rule::in($boleh)],
            'catatan' => ['nullable', 'string', 'max:1000'],
            // Data kerja saat pendaftar diterima (dasar jadwal tindak lanjut bulan ke-1, 3, 6).
            'tanggal_mulai_kerja' => ['nullable', 'required_if:status,'.StatusPenyaluran::Menyalurkan->value, 'date', 'after:-1 year', 'before:+6 months'],
            'gaji' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Tahap tersebut tidak bisa dipilih dari tahap sekarang.',
            'tanggal_mulai_kerja.required_if' => 'Isi tanggal mulai kerja.',
        ];
    }
}
