<x-layouts.app title="Lowongan Saya">
    <x-page-header title="Lowongan Saya" subtitle="Lowongan hanya dilihat petugas Polres, tidak ditampilkan ke publik.">
        @can('create', \App\Models\JobListing::class)
            <x-button :href="route('instansi.lowongan.create')">+ Upload Lowongan</x-button>
        @endcan
    </x-page-header>

    @cannot('create', \App\Models\JobListing::class)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Instansi Anda belum terverifikasi. Lowongan baru bisa diunggah setelah lolos survei petugas Polres.
            <a href="{{ route('instansi.verifikasi.edit') }}" class="font-semibold underline">Lihat data verifikasi</a>
        </div>
    @endcannot

    <x-table :paginator="$lowongan">
        <x-slot:head><th>Judul</th><th>Kategori</th><th>Kuota</th><th>Tersalur</th><th>Batas</th><th>Status</th><th></th></x-slot:head>
        @forelse ($lowongan as $item)
            <tr>
                <td class="font-medium">{{ $item->judul }}</td>
                <td>{{ $item->kategori->nama }}</td>
                <td>{{ $item->kuota }}</td>
                <td>{{ $item->jumlah_tersalur }}</td>
                <td class="whitespace-nowrap">{{ $item->batas_tanggal->format('d/m/Y') }}</td>
                <td><x-status :status="$item->status" /></td>
                <td class="text-right"><a href="{{ route('instansi.lowongan.show', $item) }}" class="text-brand-600 hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="7"><x-empty message="Belum ada lowongan." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
