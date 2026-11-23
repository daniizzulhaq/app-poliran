<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Instansi\VerifikasiInstansiRequest;

/** Instansi yang diinput petugas survei saat jemput bola (tanpa akun, langsung terverifikasi). */
class InstansiPetugasRequest extends VerifikasiInstansiRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('survei-instansi');
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
            'catatan_survei' => ['required', 'string', 'max:2000'],
        ];
    }
}
