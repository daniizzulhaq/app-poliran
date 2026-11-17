<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KecamatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-master-data');
    }

    public function rules(): array
    {
        $id = $this->route('kecamatan')?->id;

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kecamatan', 'nama')->ignore($id)],
            'kode' => ['nullable', 'string', 'max:20', Rule::unique('kecamatan', 'kode')->ignore($id)],
        ];
    }
}
