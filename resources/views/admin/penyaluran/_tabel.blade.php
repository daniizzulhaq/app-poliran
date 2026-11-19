{{-- Daftar penyaluran. Variabel: $daftar, $tampilLowongan (bool), $lowongan (bila tampilLowongan = false) --}}
<x-table :paginator="$daftar instanceof \Illuminate\Contracts\Pagination\Paginator ? $daftar : null">
    <x-slot:head>
        <th>Pendaftar</th>@if ($tampilLowongan)<th>Lowongan</th>@endif<th>Status</th><th>Hubungi</th><th>Tahap Berikutnya</th>
    </x-slot:head>
    @forelse ($daftar as $item)
        @php($lw = $tampilLowongan ? $item->jobListing : $lowongan)
        @php($pk = $item->pencariKerja)
        <tr>
            <td>
                <a href="{{ route('admin.pendaftar.show', $pk) }}" class="font-medium text-brand-700 hover:underline">{{ $pk->nama }}</a>
                <div class="text-xs text-slate-500">{{ $pk->kecamatan->nama }} · {{ $item->created_at->format('d/m/Y') }}</div>
            </td>
            @if ($tampilLowongan)
                <td>
                    <a href="{{ route('admin.lowongan.show', $lw) }}" class="text-brand-700 hover:underline">{{ $lw->judul }}</a>
                    <div class="text-xs text-slate-500">{{ $lw->instansi->nama }}</div>
                </td>
            @endif
            <td>
                <x-status :status="$item->status" />
                @if ($item->tanggal_mulai_kerja)
                    <div class="mt-1 text-xs text-slate-500">Mulai kerja {{ $item->tanggal_mulai_kerja->format('d/m/Y') }}@if ($item->gaji) · Rp{{ number_format($item->gaji, 0, ',', '.') }}/bln @endif</div>
                @endif
                @if ($item->catatan)<div class="mt-1 text-xs text-slate-500">{{ $item->catatan }}</div>@endif
            </td>
            <td class="text-xs whitespace-nowrap">
                @if ($pk->no_wa)
                    <a href="{{ \App\Support\PesanWa::url($pk->no_wa, \App\Support\PesanWa::penyaluranKePendaftar($pk, $lw, $item->status)) }}" target="_blank" rel="noopener" class="block text-green-700 hover:underline">WA pendaftar (pesan siap kirim)</a>
                    <a href="tel:{{ $pk->no_wa }}" class="block text-brand-600 hover:underline">Telepon pendaftar</a>
                @endif
                <a href="{{ \App\Support\PesanWa::url($lw->instansi->no_wa, \App\Support\PesanWa::penyaluranKeInstansi($lw->instansi, $pk, $lw)) }}" target="_blank" rel="noopener" class="block text-green-700 hover:underline">WA instansi (pesan siap kirim)</a>
                <a href="tel:{{ $lw->instansi->no_wa }}" class="block text-brand-600 hover:underline">Telepon instansi</a>
            </td>
            <td>
                @can('ubahTahap', $item)
                    <form method="POST" action="{{ route('admin.penyaluran.tahap', $item) }}" class="flex min-w-56 flex-col gap-2" x-data="{ tahap: '' }">
                        @csrf @method('PUT')
                        <select name="status" class="form-control" required aria-label="Tahap berikutnya" x-model="tahap">
                            <option value="">- Pilih tahap -</option>
                            @foreach ($item->status->berikutnya() as $tahap)
                                <option value="{{ $tahap->value }}">{{ $tahap->labelPilihan() }}</option>
                            @endforeach
                        </select>
                        <template x-if="tahap === '{{ \App\Enums\StatusPenyaluran::Menyalurkan->value }}'">
                            <div class="grid gap-2">
                                <label class="text-xs text-slate-600">Tanggal mulai kerja
                                    <input type="date" name="tanggal_mulai_kerja" value="{{ today()->format('Y-m-d') }}" class="form-control mt-1" required>
                                </label>
                                <label class="text-xs text-slate-600">Gaji per bulan (Rp, opsional)
                                    <input type="number" name="gaji" min="0" step="1000" class="form-control mt-1" placeholder="Contoh: 2500000">
                                </label>
                            </div>
                        </template>
                        <input name="catatan" class="form-control" placeholder="Catatan (opsional)" aria-label="Catatan">
                        <x-button size="sm">Simpan</x-button>
                    </form>
                @elseif ($item->status->selesai())
                    <span class="text-xs text-slate-500">Selesai {{ $item->hasil_pada?->format('d/m/Y') }}</span>
                @else
                    <span class="text-xs text-slate-500">Diperbarui {{ $item->updated_at->format('d/m/Y') }}</span>
                @endcan
            </td>
        </tr>
    @empty
        <tr><td colspan="5"><x-empty message="Belum ada penyaluran." /></td></tr>
    @endforelse
</x-table>
