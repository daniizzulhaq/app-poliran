<x-layouts.auth title="Daftar Akun" wide>
    <p class="mb-5 text-sm text-slate-500">Pilih jenis akun. Semua layanan POLIRAN gratis.</p>
    <div class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('register.pencari-kerja') }}" class="rounded-xl border-2 border-slate-200 p-5 transition hover:border-brand-500 hover:bg-brand-50">
            <p class="text-3xl">🧑‍🔧</p>
            <p class="mt-2 font-bold text-slate-900">Pencari Kerja / Pelatihan</p>
            <p class="mt-1 text-sm text-slate-600">Untuk warga TTS berusia minimal 18 tahun yang ingin ikut pelatihan dan disalurkan ke pekerjaan.</p>
        </a>
        <a href="{{ route('register.instansi') }}" class="rounded-xl border-2 border-slate-200 p-5 transition hover:border-brand-500 hover:bg-brand-50">
            <p class="text-3xl">🏢</p>
            <p class="mt-2 font-bold text-slate-900">Penyedia Kerja (Instansi)</p>
            <p class="mt-1 text-sm text-slate-600">Untuk perusahaan, UMKM, perorangan, atau instansi pemerintah yang membutuhkan pekerja.</p>
        </a>
    </div>
    <p class="mt-6 text-center text-sm text-slate-600">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Masuk</a></p>
</x-layouts.auth>
