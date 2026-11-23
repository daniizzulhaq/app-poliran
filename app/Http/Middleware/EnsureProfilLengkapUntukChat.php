<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfilLengkapUntukChat
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->bisaChat()) {
            $pesan = 'Lengkapi profil Anda terlebih dahulu untuk membuka chat dengan Admin Utama. Isi data diri, alamat di TTS, dan minat kerja agar admin bisa memeriksa kompetensi Anda.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $pesan,
                    'redirect_url' => route('pencari.profil.edit'),
                ], 403);
            }

            $lowongan = $request->route('lowongan');
            $request->session()->put('kembali_ke', $lowongan
                ? route('public.lowongan.show', $lowongan).'#chat-admin'
                : route('chat.index'));

            return redirect()->route('pencari.profil.edit')->with('error', $pesan);
        }

        return $next($request);
    }
}
