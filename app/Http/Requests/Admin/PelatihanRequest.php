<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PelatihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-pelatihan');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['bersertifikat' => $this->boolean('bersertifikat')]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'mitra' => ['nullable', 'string', 'max:150'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'date_format:H:i', 'after:jam_mulai'],
            'kecamatan_id' => ['required', 'exists:kecamatan,id'],
            'alamat' => ['required', 'string', 'max:500'],
            'kuota' => ['required', 'integer', 'min:1', 'max:1000'],
            'syarat' => ['nullable', 'string', 'max:3000'],
            'batas_pendaftaran' => ['required', 'date', 'before_or_equal:tanggal_mulai'],
            'bersertifikat' => ['boolean'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
