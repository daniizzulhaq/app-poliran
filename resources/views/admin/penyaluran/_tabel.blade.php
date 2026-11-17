{{-- Daftar penyaluran. Variabel: $daftar, $tampilLowongan (bool), $lowongan (bila tampilLowongan = false) --}}
<x-table :paginator="$daftar instanceof \Illuminate\Contracts\Pagination\Paginator ? $daftar : null">
    <x-slot:head>
        <th>Pendaftar</th>@if ($tampilLowongan)<th>Lowongan</th>@endif<th>Status</th><th>Hubungi</th><th>Hasil</th>
    </x-slot:head>
    @forelse ($daftar as $item)
        @php($lw = $tampilLowongan ? $item->jobListing : $lowongan)
        <tr>
            <td>
                <a href="{{ route('admin.pendaftar.show', $item->pencariKerja) }}" class="font-medium text-brand-700 hover:underline">{{ $item->pencariKerja->nama }}</a>
                <div class="text-xs text-slate-500">{{ $item->pencariKerja->kecamatan->nama }} · {{ $item->created_at->format('d/m/Y') }}</div>
            </td>
            @if ($tampilLowongan)
                <td>
                    <a href="{{ route('admin.lowongan.show', $lw) }}" class="text-brand-700 hover:underline">{{ $lw->judul }}</a>
                    <div class="text-xs text-slate-500">{{ $lw->instansi->nama }}</div>
                </td>
            @endif
            <td><x-status :status="$item->status" />@if ($item->catatan)<div class="mt-1 text-xs text-slate-500">{{ $item->catatan }}</div>@endif</td>
            <td class="text-xs whitespace-nowrap">
                @if ($item->pencariKerja->no_wa)
                    <a href="https://wa.me/{{ $item->pencariKerja->noWaInternasional() }}" target="_blank" rel="noopener" class="block text-green-700 hover:underline">WA pendaftar</a>
                    <a href="tel:{{ $item->pencariKerja->no_wa }}" class="block text-brand-600 hover:underline">Telepon pendaftar</a>
                @endif
                <a href="https://wa.me/{{ \App\Models\PencariKerja::formatWa($lw->instansi->no_wa) }}" target="_blank" rel="noopener" class="block text-green-700 hover:underline">WA instansi</a>
                <a href="tel:{{ $lw->instansi->no_wa }}" class="block text-brand-600 hover:underline">Telepon instansi</a>
            </td>
            <td>
                @if ($item->status === \App\Enums\StatusPenyaluran::Diproses)
                    <form method="POST" action="{{ route('admin.penyaluran.hasil', $item) }}" class="flex min-w-48 flex-col gap-2">
                        @csrf @method('PUT')
                        <select name="status" class="form-control" required>
                            <option value="">- Hasil -</option>
                            <option value="diterima">Diterima kerja</option>
                            <option value="tidak_diterima">Tidak diterima</option>
                        </select>
                        <input name="catatan" class="form-control" placeholder="Catatan (opsional)">
                        <x-button size="sm">Simpan</x-button>
                    </form>
                @else
                    <span class="text-xs text-slate-500">{{ $item->hasil_pada?->format('d/m/Y') }}</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="5"><x-empty message="Belum ada penyaluran." /></td></tr>
    @endforelse
</x-table>
