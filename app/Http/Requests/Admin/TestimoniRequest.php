<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimoniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-konten');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['aktif' => $this->boolean('aktif'), 'urutan' => (int) $this->urutan]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:150'],
            'isi' => ['required', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'urutan' => ['integer', 'min:0', 'max:999'],
            'aktif' => ['boolean'],
        ];
    }
}
