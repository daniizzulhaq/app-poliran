<x-layouts.app title="Tindak Lanjut Kerja">
    <x-page-header title="Tindak Lanjut Kerja"
        subtitle="Hubungi pekerja yang sudah disalurkan pada bulan ke-1, 3, dan 6 setelah mulai kerja, lalu catat apakah masih bekerja. Pekerja yang berhenti otomatis kembali ke daftar pencari kerja." />

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (['jatuh-tempo' => 'Jatuh tempo ('.$jumlahJatuhTempo.')', 'semua' => 'Semua pekerja tersalur'] as $nilai => $label)
            <a href="{{ route('admin.penyaluran.tindak-lanjut', ['tab' => $nilai]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $tab === $nilai,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $tab !== $nilai,
            ])>{{ $label }}</a>
        @endforeach
    </div>

    <x-table :paginator="$daftar">
        <x-slot:head><th>Pekerja</th><th>Pekerjaan</th><th>Tindak Lanjut</th><th>Hubungi</th><th>Catat Hasil</th></x-slot:head>
        @forelse ($daftar as $item)
            @php($pk = $item->pencariKerja)
            @php($lw = $item->jobListing)
            @php($berikut = $item->bulanTindakLanjutBerikutnya())
            @php($jatuhTempo = $berikut ? $item->jatuhTempo($berikut) : null)
            <tr>
                <td>
                    <a href="{{ route('admin.pendaftar.show', $pk) }}" class="font-medium text-brand-700 hover:underline">{{ $pk->nama }}</a>
                    <div class="text-xs text-slate-500">{{ $pk->kecamatan->nama }}</div>
                </td>
                <td>
                    <a href="{{ route('admin.lowongan.show', $lw) }}" class="text-brand-700 hover:underline">{{ $lw->judul }}</a>
                    <div class="text-xs text-slate-500">{{ $lw->instansi->nama }}</div>
                    <div class="text-xs text-slate-500">Mulai {{ $item->tanggal_mulai_kerja?->format('d/m/Y') ?? '-' }}@if ($item->gaji) · Rp{{ number_format($item->gaji, 0, ',', '.') }}/bln @endif</div>
                </td>
                <td class="text-xs">
                    <ul class="space-y-1">
                        @foreach (\App\Models\TindakLanjut::BULAN as $bulan)
                            @php($tl = $item->tindakLanjut->firstWhere('bulan_ke', $bulan))
                            <li class="flex flex-wrap items-center gap-1">
                                <span class="w-14 text-slate-500">Bulan {{ $bulan }}</span>
                                @if ($tl)
                                    <x-status :status="$tl->status" />
                                    @if ($tl->catatan)<span class="text-slate-500">{{ \Illuminate\Support\Str::limit($tl->catatan, 60) }}</span>@endif
                                @elseif ($bulan === $berikut)
                                    <span @class(['font-medium', 'text-red-700' => ! $jatuhTempo->isFuture(), 'text-slate-500' => $jatuhTempo->isFuture()])>
                                        {{ $jatuhTempo->isFuture() ? 'mulai' : 'jatuh tempo' }} {{ $jatuhTempo->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-xs whitespace-nowrap">
                    @if ($pk->no_wa)
                        <a href="{{ \App\Support\PesanWa::url($pk->no_wa, \App\Support\PesanWa::tindakLanjut($pk, $lw, $berikut ?? \App\Models\TindakLanjut::BULAN[0])) }}" target="_blank" rel="noopener" class="block text-green-700 hover:underline">WA pekerja (pesan siap kirim)</a>
                        <a href="tel:{{ $pk->no_wa }}" class="block text-brand-600 hover:underline">Telepon pekerja</a>
                    @endif
                    <a href="https://wa.me/{{ \App\Models\PencariKerja::formatWa($lw->instansi->no_wa) }}" target="_blank" rel="noopener" class="block text-green-700 hover:underline">WA instansi</a>
                </td>
                <td>
                    @if ($berikut && ! $jatuhTempo->isFuture())
                        @can('tindakLanjut', $item)
                            <form method="POST" action="{{ route('admin.penyaluran.tindak-lanjut.store', $item) }}" class="flex min-w-56 flex-col gap-2" x-data="{ hasil: '' }">
                                @csrf
                                <select name="status" class="form-control" required x-model="hasil" aria-label="Hasil tindak lanjut bulan ke-{{ $berikut }}">
                                    <option value="">- Hasil bulan ke-{{ $berikut }} -</option>
                                    @foreach (\App\Enums\StatusTindakLanjut::cases() as $s)
                                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                                <input name="catatan" class="form-control" aria-label="Catatan"
                                    :required="hasil === '{{ \App\Enums\StatusTindakLanjut::Berhenti->value }}'"
                                    :placeholder="hasil === '{{ \App\Enums\StatusTindakLanjut::Berhenti->value }}' ? 'Alasan berhenti (wajib)' : 'Catatan (opsional)'">
                                <x-button size="sm">Simpan</x-button>
                            </form>
                        @endcan
                    @elseif ($berikut)
                        <span class="text-xs text-slate-500">Bulan ke-{{ $berikut }} mulai {{ $jatuhTempo->format('d/m/Y') }}</span>
                    @elseif ($item->tindakLanjut->contains('status', \App\Enums\StatusTindakLanjut::Berhenti))
                        <span class="text-xs text-red-700">Sudah berhenti bekerja</span>
                    @else
                        <span class="text-xs text-green-700">Tindak lanjut selesai</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5"><x-empty :message="$tab === 'jatuh-tempo' ? 'Tidak ada tindak lanjut yang jatuh tempo.' : 'Belum ada pekerja tersalur.'" /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
