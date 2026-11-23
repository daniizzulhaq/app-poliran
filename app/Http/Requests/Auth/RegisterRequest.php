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
            'tanggal_lahir' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.today()->subYearsNoOverflow(18)->toDateString()],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'persetujuan' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Nomor WhatsApp harus diawali 08 atau 62, contoh 081234567890.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi untuk memastikan usia Anda minimal 18 tahun.',
            'tanggal_lahir.date_format' => 'Masukkan tanggal lahir yang valid.',
            'tanggal_lahir.before_or_equal' => 'Pendaftar harus berusia minimal 18 tahun.',
        ];
    }
}
