<x-layouts.app title="Survei Lowongan">
    <x-page-header title="Survei Lowongan" subtitle="Periksa apakah lowongan aman: gaji wajar, tidak meminta uang, bukan penipuan." />

    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul lowongan" class="form-control max-w-xs">
        <x-button variant="secondary">Cari</x-button>
    </form>

    <x-table :paginator="$lowongan">
        <x-slot:head><th>Lowongan</th><th>Instansi</th><th>Kategori</th><th>Gaji</th><th>Kuota</th><th>Dikirim</th><th></th></x-slot:head>
        @forelse ($lowongan as $item)
            <tr>
                <td class="font-medium">{{ $item->judul }}</td>
                <td>{{ $item->instansi->nama }}</td>
                <td>{{ $item->kategori->nama }}</td>
                <td class="whitespace-nowrap">{{ $item->gajiLabel() }}</td>
                <td>{{ $item->kuota }}</td>
                <td class="whitespace-nowrap">{{ $item->updated_at->format('d/m/Y') }}</td>
                <td class="text-right"><a href="{{ route('admin.lowongan.show', $item) }}" class="text-brand-600 hover:underline">Survei</a></td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty message="Tidak ada lowongan yang menunggu survei." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
