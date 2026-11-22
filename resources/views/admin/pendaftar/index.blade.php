<x-layouts.app title="Pendaftar">
    <x-page-header title="Pendaftar" :subtitle="$semuaKecamatan ? 'Semua kecamatan.' : 'Hanya pendaftar dari kecamatan tugas Anda.'">
        <x-button :href="route('admin.pendaftar.create')">+ Tambah Pendaftar Manual</x-button>
    </x-page-header>

    <form method="GET" class="mb-4 grid gap-2 sm:grid-cols-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIK lengkap" class="form-control">
        <select name="status" class="form-control">
            <option value="">Semua status</option>
            @foreach (\App\Enums\StatusPendaftar::cases() as $s)
                <option value="{{ $s->value }}" @selected($status === $s)>{{ $s->label() }}</option>
            @endforeach
            <option value="menunggu_lama" @selected($menungguLama)>Belum diproses &gt; {{ \App\Models\PencariKerja::BATAS_HARI_MENUNGGU }} hari</option>
        </select>
        <select name="kecamatan_id" class="form-control">
            <option value="">Semua kecamatan</option>
            @foreach ($kecamatan as $id => $nama)
                <option value="{{ $id }}" @selected(request('kecamatan_id') == $id)>{{ $nama }}</option>
            @endforeach
        </select>
        <x-button variant="secondary">Filter</x-button>
    </form>

    @if ($menungguLama)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
            Pendaftar berstatus <strong>Terdaftar</strong> yang tidak ada perkembangan selama lebih dari {{ \App\Models\PencariKerja::BATAS_HARI_MENUNGGU }} hari
            (urut dari yang paling lama). Hubungi mereka, tawarkan pelatihan, atau cocokkan dengan lowongan.
        </div>
    @endif

    <x-table :paginator="$pendaftar">
        <x-slot:head><th>Nama</th><th>NIK</th><th>Kecamatan / Desa</th><th>Pendidikan</th><th>Status</th><th>Terdaftar</th><th></th></x-slot:head>
        @forelse ($pendaftar as $item)
            <tr>
                <td class="font-medium">{{ $item->nama }}</td>
                <td class="font-mono text-xs">{{ $item->nik }}</td>
                <td>{{ $item->kecamatan->nama }}<div class="text-xs text-slate-500">{{ $item->desa->nama }}</div></td>
                <td>{{ $item->pendidikan->label() }}</td>
                <td><x-status :status="$item->status" /></td>
                <td class="whitespace-nowrap">{{ $item->created_at->format('d/m/Y') }}</td>
                <td class="text-right"><a href="{{ route('admin.pendaftar.show', $item) }}" class="text-brand-600 hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty message="Belum ada pendaftar." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
