<?php

namespace App\Http\Requests\Instansi;

use App\Enums\JenisDokumen;
use App\Enums\JenisInstansi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiInstansiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $instansi = $this->user()->instansi;

        return $instansi === null || $this->user()->can('update', $instansi);
    }

    public function rules(): array
    {
        $baru = $this->user()->instansi === null;

        return [
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', Rule::enum(JenisInstansi::class)],
            'penanggung_jawab' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:500'],
            'no_wa' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'jenis_dokumen' => ['required', Rule::enum(JenisDokumen::class)],
            'nomor_dokumen' => ['required', 'string', 'max:50', 'regex:/^[0-9A-Za-z.\-\/]+$/'],
            'file_dokumen' => [$baru ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'file_selfie' => [$baru ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Tentukan lokasi usaha di peta (klik peta atau tekan "Ambil Lokasi Saya").',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali 08 atau 62.',
        ];
    }
}
