<x-layouts.app title="Laporan & Export">
    <x-page-header title="Laporan & Export" subtitle="Rekap pendaftar per kecamatan dan per status." />

    <x-card title="Export" class="mb-4">
        <form method="GET" action="{{ route('admin.laporan.excel') }}" class="grid gap-3 sm:grid-cols-4 sm:items-end">
            <x-select label="Status (daftar pendaftar)" name="status" :options="\App\Enums\StatusPendaftar::options()" placeholder="Semua status" />
            <x-select label="Kecamatan (daftar pendaftar)" name="kecamatan_id" :options="$kecamatan->all()" placeholder="Semua kecamatan" />
            <x-button>Unduh Excel</x-button>
            <x-button :href="route('admin.laporan.pdf')" variant="secondary">Unduh PDF Rekap</x-button>
        </form>
        <p class="mt-2 text-xs text-slate-500">Excel berisi 2 sheet: rekap per kecamatan & status, serta daftar pendaftar (NIK disensor).</p>
    </x-card>

    <div class="mb-4 grid grid-cols-2 gap-4 md:grid-cols-6">
        @foreach (\App\Enums\StatusPendaftar::cases() as $s)
            <x-stat :label="$s->label()" :value="$perStatus[$s->value]" :color="$s->color()" />
        @endforeach
    </div>

    <x-card title="Bertahan Bekerja (Tindak Lanjut)" :padding="false" class="mb-4">
        <x-slot:actions><x-button :href="route('admin.penyaluran.tindak-lanjut')" size="sm" variant="secondary">Tindak Lanjut Kerja</x-button></x-slot:actions>
        <div class="overflow-x-auto">@include('admin.laporan._tabel-retensi')</div>
    </x-card>

    <x-card title="Rekap per Kecamatan" :padding="false">
        <div class="overflow-x-auto">@include('admin.laporan._tabel-rekap')</div>
    </x-card>
</x-layouts.app>
