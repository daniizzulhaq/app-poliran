<x-layouts.app title="Lowongan">
    <x-page-header title="Lowongan" subtitle="Data internal. Lowongan tidak ditampilkan ke publik maupun pencari kerja.">
        @can('survei-lowongan')
            <x-button :href="route('admin.lowongan.create')">+ Input Lowongan (Jemput Bola)</x-button>
        @endcan
    </x-page-header>

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (\App\Enums\StatusLowongan::cases() as $s)
            <a href="{{ route('admin.lowongan.index', ['status' => $s->value]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }}</a>
        @endforeach
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="hidden" name="status" value="{{ $status->value }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul" class="form-control max-w-xs">
        <select name="kategori_id" class="form-control max-w-xs">
            <option value="">Semua kategori</option>
            @foreach ($kategori as $id => $nama)
                <option value="{{ $id }}" @selected(request('kategori_id') == $id)>{{ $nama }}</option>
            @endforeach
        </select>
        <x-button variant="secondary">Filter</x-button>
    </form>

    <x-table :paginator="$lowongan">
        <x-slot:head><th>Lowongan</th><th>Instansi</th><th>Kategori</th><th>Kuota</th><th>Tersalur</th><th>Batas</th><th>Status</th><th></th></x-slot:head>
        @forelse ($lowongan as $item)
            <tr>
                <td class="font-medium">
                    {{ $item->judul }}
                    @if ($item->wilayah_kerja->perluCekLegalitas())<x-badge :color="$item->wilayah_kerja->color()" class="ml-1">{{ $item->wilayah_kerja->label() }}</x-badge>@endif
                </td>
                <td>{{ $item->instansi->nama }}</td>
                <td>{{ $item->kategori->nama }}</td>
                <td>{{ $item->kuota }}</td>
                <td>{{ $item->jumlah_tersalur }}</td>
                <td class="whitespace-nowrap">{{ $item->batas_tanggal->format('d/m/Y') }}</td>
                <td><x-status :status="$item->status" /></td>
                <td class="text-right"><a href="{{ route('admin.lowongan.show', $item) }}" class="text-brand-600 hover:underline">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="8"><x-empty /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
