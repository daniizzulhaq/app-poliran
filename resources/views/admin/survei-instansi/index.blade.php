<x-layouts.app title="Survei Instansi">
    <x-page-header title="Survei Instansi" subtitle="Periksa data, dokumen, dan lokasi instansi yang mendaftar." />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (\App\Enums\StatusInstansi::cases() as $s)
            <a href="{{ route('admin.survei-instansi.index', ['status' => $s->value]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }} ({{ $jumlah[$s->value] ?? 0 }})</a>
        @endforeach
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="hidden" name="status" value="{{ $status->value }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama instansi" class="form-control max-w-xs">
        <x-button variant="secondary">Cari</x-button>
    </form>

    <x-table :paginator="$instansi">
        <x-slot:head><th>Instansi</th><th>Jenis</th><th>Penanggung Jawab</th><th>Diajukan</th><th>Status</th><th></th></x-slot:head>
        @forelse ($instansi as $item)
            <tr>
                <td class="font-medium">{{ $item->nama }}<div class="text-xs text-slate-500">{{ $item->user->email }}</div></td>
                <td>{{ $item->jenis->label() }}</td>
                <td>{{ $item->penanggung_jawab }}</td>
                <td class="whitespace-nowrap">{{ $item->diajukan_pada?->format('d/m/Y H:i') }}</td>
                <td><x-status :status="$item->status" /></td>
                <td class="text-right"><a href="{{ route('admin.survei-instansi.show', $item) }}" class="text-brand-600 hover:underline">{{ $item->status === \App\Enums\StatusInstansi::MenungguSurvey ? 'Survei' : 'Detail' }}</a></td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty message="Tidak ada instansi pada status ini." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
