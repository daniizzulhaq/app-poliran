<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-master-data');
    }

    public function rules(): array
    {
        $kecamatan = $this->route('kecamatan');

        return [
            'nama' => ['required', 'string', 'max:100',
                Rule::unique('desa', 'nama')->where('kecamatan_id', $kecamatan->id)->ignore($this->route('desa')?->id)],
            'kode' => ['nullable', 'string', 'max:20'],
        ];
    }
}
