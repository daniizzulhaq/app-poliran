<?php

namespace App\Http\Requests\Instansi;

use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Enums\TipeKerja;
use App\Enums\WilayahKerja;
use App\Models\JobListing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LowonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lowongan = $this->route('lowongan');

        return $lowongan
            ? $this->user()->can('update', $lowongan)
            : $this->user()->can('create', JobListing::class);
    }

    protected function prepareForValidation(): void
    {
        $angka = fn ($v) => $v === null || $v === '' ? null : preg_replace('/\D/', '', (string) $v);
        $this->merge([
            'gaji_min' => $angka($this->gaji_min),
            'gaji_max' => $angka($this->gaji_max),
            'gaji_nego' => $this->boolean('gaji_nego'),
            'nomor_izin_p3mi' => $this->input('wilayah_kerja') === WilayahKerja::LuarNegeri->value ? $this->input('nomor_izin_p3mi') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'skills' => ['array'],
            'skills.*' => ['integer', 'exists:skill,id'],
            'jobdesk' => ['required', 'string', 'max:5000'],
            'pendidikan_min' => ['nullable', Rule::enum(Pendidikan::class)],
            'umur_min' => ['nullable', 'integer', 'min:15', 'max:70'],
            'umur_max' => ['nullable', 'integer', 'min:15', 'max:70', 'gte:umur_min'],
            'jenis_kelamin' => ['nullable', Rule::enum(JenisKelamin::class)],
            'pengalaman' => ['nullable', 'string', 'max:2000'],
            'syarat_lain' => ['nullable', 'string', 'max:2000'],
            'gaji_nego' => ['boolean'],
            'gaji_min' => ['nullable', 'required_unless:gaji_nego,true', 'integer', 'min:0'],
            'gaji_max' => ['nullable', 'integer', 'gte:gaji_min'],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
            'ketentuan_lain' => ['nullable', 'string', 'max:2000'],
            'tipe_kerja' => ['required', Rule::enum(TipeKerja::class)],
            'lokasi_kerja' => ['required', 'string', 'max:200'],
            'wilayah_kerja' => ['required', Rule::enum(WilayahKerja::class)],
            'nomor_izin_p3mi' => ['nullable', 'required_if:wilayah_kerja,'.WilayahKerja::LuarNegeri->value, 'string', 'max:100'],
            'kuota' => ['required', 'integer', 'min:1', 'max:1000'],
            'batas_tanggal' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'gaji_min.required_unless' => 'Isi gaji minimal, atau centang "Gaji bisa nego".',
            'nomor_izin_p3mi.required_if' => 'Lowongan luar negeri wajib mencantumkan nomor izin perusahaan penempatan (P3MI).',
        ];
    }
}
