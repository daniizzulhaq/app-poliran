<x-layouts.app title="Penyaluran">
    <x-page-header title="Penyaluran" subtitle="Hubungi instansi dan pendaftar lewat WA/telepon, lalu catat hasilnya. Untuk menyalurkan, buka Lowongan > Pendaftar Cocok." />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (\App\Enums\StatusPenyaluran::cases() as $s)
            <a href="{{ route('admin.penyaluran.index', ['status' => $s->value]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }}</a>
        @endforeach
    </div>

    @include('admin.penyaluran._tabel', ['daftar' => $daftar, 'tampilLowongan' => true])
</x-layouts.app>
