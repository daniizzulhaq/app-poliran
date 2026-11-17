<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validasi master data sederhana (kategori, skill) yang hanya punya kolom nama. */
class MasterNamaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-master-data');
    }

    public function rules(): array
    {
        $routeName = $this->route()->getName();
        [$tabel, $param] = str_contains($routeName, 'kategori') ? ['kategori', 'kategori'] : ['skill', 'skill'];

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique($tabel, 'nama')->ignore($this->route($param)?->id)],
        ];
    }
}
