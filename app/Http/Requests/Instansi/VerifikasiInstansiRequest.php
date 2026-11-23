<?php

namespace App\Http\Requests\Instansi;

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
        return [
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', Rule::enum(JenisInstansi::class)],
            'penanggung_jawab' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.today()->subYearsNoOverflow(18)->toDateString()],
            'alamat' => ['required', 'string', 'max:500'],
            'no_wa' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_lahir.required' => 'Tanggal lahir penanggung jawab wajib diisi.',
            'tanggal_lahir.date_format' => 'Masukkan tanggal lahir yang valid.',
            'tanggal_lahir.before_or_equal' => 'Penanggung jawab penyedia kerja harus berusia minimal 18 tahun.',
            'latitude.required' => 'Tentukan lokasi usaha di peta (klik peta atau tekan "Ambil Lokasi Saya").',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali 08 atau 62.',
        ];
    }
}
