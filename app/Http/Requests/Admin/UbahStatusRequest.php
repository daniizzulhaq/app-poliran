<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusPendaftar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbahStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek PencariKerjaPolicy::ubahStatus di controller
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(StatusPendaftar::class)],
            'catatan' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return ['catatan.required' => 'Catatan perubahan status wajib diisi.'];
    }
}
