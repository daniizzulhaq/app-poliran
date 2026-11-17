<?php

namespace App\Http\Requests;

use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Models\PencariKerja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Data profil pendaftar. Dipakai pencari kerja (profil sendiri) dan admin (input manual / ubah).
 * Alamat khusus TTS: desa wajib milik kecamatan yang dipilih.
 */
class PencariKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $pencari = $this->pencariYangDiubah();

        if ($this->routeIs('pencari.*')) {
            return $user->can('daftar-pelatihan') && ($pencari === null || $user->can('update', $pencari));
        }

        return $pencari ? $user->can('update', $pencari) : $user->can('kelola-pendaftar');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['nik' => preg_replace('/\D/', '', (string) $this->nik)]);
    }

    public function rules(): array
    {
        $pencari = $this->pencariYangDiubah();
        $user = $this->user();

        $kecamatanRule = ['required', 'integer', 'exists:kecamatan,id'];
        if (! $this->routeIs('pencari.*') && ! $user->isSuperAdmin()) {
            $kecamatanRule[] = Rule::in($user->kecamatanIds());
        }

        return [
            'nama' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'digits:16', Rule::unique('pencari_kerja', 'nik')->ignore($pencari?->id)],
            'jenis_kelamin' => ['required', Rule::enum(JenisKelamin::class)],
            'tanggal_lahir' => ['required', 'date', 'before:-14 years', 'after:-80 years'],
            'pendidikan' => ['required', Rule::enum(Pendidikan::class)],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'no_wa' => [$this->routeIs('pencari.*') ? 'required' : 'nullable', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'kecamatan_id' => $kecamatanRule,
            'desa_id' => ['required', 'integer',
                Rule::exists('desa', 'id')->where('kecamatan_id', (int) $this->input('kecamatan_id'))],
            'alamat_detail' => ['required', 'string', 'max:500'],
            'kategori' => ['required', 'array', 'min:1'],
            'kategori.*' => ['integer', 'exists:kategori,id'],
            'skills' => ['array'],
            'skills.*' => ['integer', 'exists:skill,id'],
            'kemampuan_lain' => ['nullable', 'string', 'max:2000'],
            'pengalaman_kerja' => ['nullable', 'string', 'max:3000'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.unique' => 'NIK sudah terdaftar. Satu NIK hanya bisa dipakai satu pendaftar.',
            'nik.digits' => 'NIK harus 16 digit angka.',
            'desa_id.exists' => 'Desa harus berada di kecamatan yang dipilih.',
            'kecamatan_id.in' => 'Anda hanya bisa menambah pendaftar di kecamatan tugas Anda.',
            'tanggal_lahir.before' => 'Umur minimal 15 tahun.',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali 08 atau 62.',
            'kategori.required' => 'Pilih minimal satu kategori minat.',
        ];
    }

    public function pencariYangDiubah(): ?PencariKerja
    {
        if ($this->routeIs('pencari.*')) {
            return $this->user()->pencariKerja;
        }

        return $this->route('pencariKerja');
    }
}
