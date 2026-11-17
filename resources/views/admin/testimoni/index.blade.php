<x-layouts.app title="Cerita Sukses">
    <x-page-header title="Cerita Sukses" subtitle="Testimoni yang aktif tampil di halaman awal (maksimal 6, urut dari angka urutan terkecil).">
        <x-button :href="route('admin.testimoni.create')">+ Tambah</x-button>
    </x-page-header>

    <x-table :paginator="$testimoni">
        <x-slot:head><th>Urutan</th><th>Nama</th><th>Isi</th><th>Status</th><th class="text-right">Aksi</th></x-slot:head>
        @forelse ($testimoni as $item)
            <tr>
                <td>{{ $item->urutan }}</td>
                <td class="font-medium">{{ $item->nama }}<div class="text-xs text-slate-500">{{ $item->keterangan }}</div></td>
                <td class="max-w-md text-xs">{{ \Illuminate\Support\Str::limit($item->isi, 120) }}</td>
                <td>@if ($item->aktif)<x-badge color="green">Tampil</x-badge>@else<x-badge>Disembunyikan</x-badge>@endif</td>
                <td class="text-right whitespace-nowrap">
                    <a href="{{ route('admin.testimoni.edit', $item) }}" class="text-brand-600 hover:underline">Ubah</a>
                    <form method="POST" action="{{ route('admin.testimoni.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus cerita ini?')">
                        @csrf @method('DELETE')
                        <button class="ml-3 text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5"><x-empty message="Belum ada cerita sukses." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
