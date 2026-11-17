<x-layouts.app title="Peserta Pelatihan">
    <x-page-header title="Peserta Pelatihan" subtitle="Pilih pelatihan untuk menerima/menolak peserta dan mengisi hasil. Admin Lapangan hanya melihat peserta dari kecamatannya." />

    <x-table :paginator="$pelatihan">
        <x-slot:head><th>Pelatihan</th><th>Tanggal</th><th>Status</th><th>Peserta</th><th>Menunggu seleksi</th><th></th></x-slot:head>
        @forelse ($pelatihan as $item)
            <tr>
                <td class="font-medium">{{ $item->nama }}<div class="text-xs text-slate-500">{{ $item->kategori->nama }} · Kec. {{ $item->kecamatan->nama }}</div></td>
                <td class="whitespace-nowrap">{{ $item->tanggal_mulai->format('d/m/Y') }}</td>
                <td><x-status :status="$item->status" /></td>
                <td>{{ $item->peserta_count }}</td>
                <td>@if ($item->menunggu_count)<x-badge color="yellow">{{ $item->menunggu_count }}</x-badge>@else 0 @endif</td>
                <td class="text-right"><a href="{{ route('admin.peserta.pelatihan', $item) }}" class="text-brand-600 hover:underline">Kelola</a></td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty message="Belum ada pelatihan." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
