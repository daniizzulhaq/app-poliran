<div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-stat label="Lowongan aktif" :value="$ringkasan['per_status']['aktif'] ?? 0" color="green" :href="route('instansi.lowongan.index')" />
    <x-stat label="Menunggu survei" :value="$ringkasan['per_status']['menunggu_survey'] ?? 0" color="yellow" />
    <x-stat label="Total kuota" :value="$ringkasan['kuota']" />
    <x-stat label="Pekerja tersalur" :value="$ringkasan['tersalur']" color="blue" />
</div>

<x-card title="Lowongan Terbaru">
    <x-slot:actions>
        @if ($instansi->isTerverifikasi())
            <x-button :href="route('instansi.lowongan.create')" size="sm">+ Upload Lowongan</x-button>
        @endif
    </x-slot:actions>
    @forelse ($terbaru as $item)
        <a href="{{ route('instansi.lowongan.show', $item) }}" class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0 hover:bg-slate-50">
            <span class="font-medium">{{ $item->judul }}</span>
            <span class="flex items-center gap-3 text-xs text-slate-500">{{ $item->jumlah_tersalur }}/{{ $item->kuota }} tersalur <x-status :status="$item->status" /></span>
        </a>
    @empty
        <p class="text-sm text-slate-500">Belum ada lowongan.</p>
    @endforelse
</x-card>
