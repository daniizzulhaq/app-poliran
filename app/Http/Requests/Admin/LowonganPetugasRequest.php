<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusInstansi;
use App\Enums\WilayahKerja;
use App\Http\Requests\Instansi\LowonganRequest;
use Illuminate\Validation\Rule;

/** Lowongan yang diinput petugas survei atas nama instansi terverifikasi (jemput bola). */
class LowonganPetugasRequest extends LowonganRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('survei-lowongan');
    }

    public function rules(): array
    {
        return parent::rules() + [
            'instansi_id' => ['required', 'integer', Rule::exists('instansi', 'id')->where('status', StatusInstansi::Terverifikasi->value)],
            'legalitas_diperiksa' => Rule::when(
                $this->input('wilayah_kerja') !== WilayahKerja::DalamTts->value,
                ['required', 'accepted'],
                ['nullable', 'boolean'],
            ),
            'catatan_survei' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'instansi_id.exists' => 'Pilih instansi yang sudah terverifikasi.',
            'legalitas_diperiksa.required' => 'Lowongan di luar TTS wajib diperiksa legalitasnya. Centang daftar periksa.',
            'legalitas_diperiksa.accepted' => 'Lowongan di luar TTS wajib diperiksa legalitasnya. Centang daftar periksa.',
        ];
    }
}
