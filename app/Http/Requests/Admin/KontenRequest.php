<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class KontenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-konten');
    }

    public function rules(): array
    {
        return [
            'hero_judul' => ['required', 'string', 'max:150'],
            'hero_teks' => ['required', 'string', 'max:1000'],
            'sambutan_nama' => ['required', 'string', 'max:100'],
            'sambutan_pangkat' => ['required', 'string', 'max:100'],
            'sambutan_isi' => ['required', 'string', 'max:5000'],
            'sambutan_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'program_teks' => ['required', 'string', 'max:5000'],
            'kontak_alamat' => ['required', 'string', 'max:300'],
            'kontak_telepon' => ['nullable', 'string', 'max:50'],
            'kontak_wa' => ['nullable', 'string', 'max:50'],
            'kontak_email' => ['nullable', 'string', 'max:150'],
            'kontak_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'kontak_longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
