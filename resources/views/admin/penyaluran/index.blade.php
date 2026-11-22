<x-layouts.app title="Penyaluran">
    <x-page-header title="Penyaluran" subtitle="Alur: Merekrut → Melatih (Bersertifikat / Non Bersertifikat) → Menyalurkan. Pelatihan boleh dilewati. Hubungi instansi dan pendaftar lewat WA/telepon, lalu pilih tahap berikutnya. Untuk merekrut pendaftar baru, buka Lowongan > Pendaftar Cocok." />

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.penyaluran.index', ['status' => $status->value, 'dari_admin_utama' => $dariAdminUtama ? null : 1]) }}" class="rounded-full border border-brand-200 bg-brand-50 px-3 py-1.5 text-sm font-semibold text-brand-800">{{ $dariAdminUtama ? 'Tampilkan Semua Rekrutmen' : 'Rekrutmen dari Admin Utama' }}</a>
        @foreach (\App\Enums\StatusPenyaluran::cases() as $s)
            <a href="{{ route('admin.penyaluran.index', ['status' => $s->value, 'dari_admin_utama' => $dariAdminUtama ? 1 : null]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }} <span @class(['ml-1 tabular-nums', 'text-white/80' => $status === $s, 'text-slate-400' => $status !== $s])>{{ $jumlah[$s->value] ?? 0 }}</span></a>
        @endforeach
    </div>

    @include('admin.penyaluran._tabel', ['daftar' => $daftar, 'tampilLowongan' => true])
</x-layouts.app>
