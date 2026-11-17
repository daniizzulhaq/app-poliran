<x-layouts.app title="Pelatihan">
    <x-page-header title="Pelatihan" subtitle="Pelatihan diselenggarakan Polres TTS (bersama mitra). Status Dibuka tampil di halaman publik.">
        <x-button :href="route('admin.pelatihan.create')">+ Buat Pelatihan</x-button>
    </x-page-header>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.pelatihan.index') }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-brand-700 text-white' => ! $status, 'bg-white ring-1 ring-slate-200' => $status])>Semua</a>
        @foreach (\App\Enums\StatusPelatihan::cases() as $s)
            <a href="{{ route('admin.pelatihan.index', ['status' => $s->value]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }}</a>
        @endforeach
    </div>

    <x-table :paginator="$pelatihan">
        <x-slot:head><th>Pelatihan</th><th>Tanggal</th><th>Lokasi</th><th>Peserta</th><th>Status</th><th></th></x-slot:head>
        @forelse ($pelatihan as $item)
            <tr>
                <td class="font-medium">{{ $item->nama }}<div class="text-xs text-slate-500">{{ $item->kategori->nama }}{{ $item->bersertifikat ? ' · bersertifikat' : '' }}</div></td>
                <td class="whitespace-nowrap">{{ $item->tanggal_mulai->format('d/m/Y') }}</td>
                <td>{{ $item->kecamatan->nama }}</td>
                <td>{{ $item->peserta_diterima_count }}/{{ $item->kuota }} <span class="text-xs text-slate-500">({{ $item->peserta_count }} daftar)</span></td>
                <td><x-status :status="$item->status" /></td>
                <td class="text-right"><a href="{{ route('admin.pelatihan.show', $item) }}" class="text-brand-600 hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty message="Belum ada pelatihan." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
