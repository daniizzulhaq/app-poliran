<ol class="grid gap-4 sm:grid-cols-4">
    @foreach ([['1', 'Daftar', 'Buat akun dan lengkapi profil, alamat di TTS, minat, dan kemampuan.'], ['2', 'Dilatih', 'Ikut pelatihan keterampilan gratis dari Polres TTS dan mitra.'], ['3', 'Bersertifikat', 'Peserta yang lulus mendapat sertifikat resmi yang bisa diunduh.'], ['4', 'Disalurkan', 'Petugas menyalurkan Anda ke penyedia kerja yang sudah disurvei.']] as [$no, $judul, $teks])
        <li class="relative rounded-xl border border-slate-200 bg-white p-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gold-400 text-lg font-extrabold text-brand-900">{{ $no }}</span>
            <p class="mt-3 font-bold">{{ $judul }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ $teks }}</p>
        </li>
    @endforeach
</ol>
