<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kelola-admin');
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $adminRoles = array_map(fn (Role $r) => $r->value, Role::adminRoles());

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in($adminRoles)],
            'kecamatan' => ['array', Rule::requiredIf($this->input('role') === Role::AdminLapangan->value)],
            'kecamatan.*' => ['integer', 'exists:kecamatan,id'],
            'boleh_kelola_pelatihan' => ['boolean'],
            'is_active' => ['boolean'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return ['kecamatan.required' => 'Admin Lapangan wajib diberi minimal 1 kecamatan.'];
    }
}
