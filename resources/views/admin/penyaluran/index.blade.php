<x-layouts.app title="Penyaluran">
    <x-page-header title="Penyaluran" subtitle="Alur: Merekrut → Melatih (Bersertifikat / Non Bersertifikat) → Menyalurkan. Pelatihan boleh dilewati. Hubungi instansi dan pendaftar lewat WA/telepon, lalu pilih tahap berikutnya. Untuk merekrut pendaftar baru, buka Lowongan > Pendaftar Cocok." />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (\App\Enums\StatusPenyaluran::cases() as $s)
            <a href="{{ route('admin.penyaluran.index', ['status' => $s->value]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }} <span @class(['ml-1 tabular-nums', 'text-white/80' => $status === $s, 'text-slate-400' => $status !== $s])>{{ $jumlah[$s->value] ?? 0 }}</span></a>
        @endforeach
    </div>

    @include('admin.penyaluran._tabel', ['daftar' => $daftar, 'tampilLowongan' => true])
</x-layouts.app>
