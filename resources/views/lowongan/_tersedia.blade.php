<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h2 class="text-2xl font-extrabold">Lowongan Pekerjaan Tersedia</h2>
        <p class="mt-2 text-sm text-slate-600">Pilih pekerjaan yang Anda minati, lalu chat Admin Utama untuk memeriksa kompetensi Anda.</p>
    </div>
    <a href="{{ route('public.lowongan.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">Lihat semua &rarr;</a>
</div>
@if ($lowongan->isEmpty())
    <x-empty class="mt-6" message="Belum ada lowongan tersedia. Silakan cek kembali nanti." />
@else
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($lowongan as $item)
            <x-lowongan-card :lowongan="$item" />
        @endforeach
    </div>
@endif
