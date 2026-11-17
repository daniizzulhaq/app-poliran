<x-layouts.app :title="$lowongan->judul">
    <x-page-header :title="$lowongan->judul" :back="route('instansi.lowongan.index')">
        @can('update', $lowongan)
            <x-button :href="route('instansi.lowongan.edit', $lowongan)" variant="secondary">Ubah</x-button>
        @endcan
        @can('tutup', $lowongan)
            <form method="POST" action="{{ route('instansi.lowongan.tutup', $lowongan) }}" onsubmit="return confirm('Tutup lowongan ini?')">
                @csrf
                <x-button variant="danger">Tutup Lowongan</x-button>
            </form>
        @endcan
    </x-page-header>

    <div class="mb-4 grid gap-4 sm:grid-cols-3">
        <x-stat label="Status" :value="$lowongan->status->label()" :color="$lowongan->status->color()" />
        <x-stat label="Kuota" :value="$lowongan->kuota" />
        <x-stat label="Tersalur" :value="$lowongan->jumlah_tersalur" color="green" />
    </div>

    @if ($lowongan->alasan_tolak)
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"><strong>Alasan ditolak:</strong> {{ $lowongan->alasan_tolak }} <br>Silakan ubah lowongan lalu kirim ulang.</div>
    @endif

    <div class="grid gap-4 lg:grid-cols-3">
        <x-card title="Detail Lowongan" class="lg:col-span-2">
            @include('lowongan._detail')
        </x-card>
        <x-card title="Pekerja yang Disalurkan">
            @forelse ($tersalur as $item)
                <div class="border-b border-slate-100 py-2 text-sm last:border-0">
                    <p class="font-medium">{{ $item->pencariKerja->nama }}</p>
                    <p class="text-xs text-slate-500">{{ $item->pencariKerja->jenis_kelamin->label() }} · diterima {{ $item->hasil_pada?->format('d/m/Y') }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada pekerja yang disalurkan. Petugas Polres akan menghubungi Anda.</p>
            @endforelse
        </x-card>
    </div>
</x-layouts.app>
