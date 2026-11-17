<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/** Daftar akun pencari kerja / instansi. */
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'persetujuan' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Nomor WhatsApp harus diawali 08 atau 62, contoh 081234567890.'];
    }
}
