@props(['lowongan'])
<article class="flex h-full flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-center gap-2">
        <x-badge color="green">Tersedia</x-badge>
        <span class="text-xs text-slate-500">{{ $lowongan->kategori->nama }}</span>
    </div>
    <h3 class="mt-3 text-lg font-bold">{{ $lowongan->judul }}</h3>
    <p class="mt-1 text-sm text-slate-600">{{ $lowongan->lokasi_kerja }} · {{ $lowongan->tipe_kerja->label() }}</p>
    <p class="mt-3 font-semibold text-brand-700">{{ $lowongan->gajiLabel() }}</p>
    <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($lowongan->jobdesk, 140) }}</p>
    <p class="mt-2 text-xs text-slate-500">Kemampuan: {{ $lowongan->skills->pluck('nama')->implode(', ') ?: 'Dikonsultasikan dengan admin' }}</p>
    <p class="mt-3 text-xs text-slate-500">{{ $lowongan->sisaKuota() }} tempat · Sampai {{ $lowongan->batas_tanggal->translatedFormat('d F Y') }}</p>
    <div class="mt-auto pt-4">
        <x-button :href="route('public.lowongan.show', $lowongan)" class="w-full">Lihat Lowongan & Chat Admin</x-button>
        <p class="mt-2 text-center text-xs text-slate-500">Dilayani langsung oleh Admin Utama POLIRAN.</p>
    </div>
</article>
