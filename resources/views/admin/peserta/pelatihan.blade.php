<x-layouts.app :title="'Peserta: '.$pelatihan->nama">
    <x-page-header :title="'Peserta: '.$pelatihan->nama" :subtitle="$pelatihan->kategori->nama.' · '.($pelatihan->bersertifikat ? 'bersertifikat' : 'tanpa sertifikat').' · kuota '.$pelatihan->kuota" :back="route('admin.peserta.index')" />

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.peserta.pelatihan', $pelatihan) }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-brand-700 text-white' => ! $status, 'bg-white ring-1 ring-slate-200' => $status])>Semua</a>
        @foreach (\App\Enums\StatusPeserta::cases() as $s)
            <a href="{{ route('admin.peserta.pelatihan', [$pelatihan, 'status' => $s->value]) }}" @class([
                'rounded-full px-3 py-1.5 text-sm font-medium',
                'bg-brand-700 text-white' => $status === $s,
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50' => $status !== $s,
            ])>{{ $s->label() }}</a>
        @endforeach
    </div>

    <x-table :paginator="$peserta">
        <x-slot:head><th>Peserta</th><th>Alasan & pengalaman</th><th>Status</th><th>Aksi</th></x-slot:head>
        @forelse ($peserta as $item)
            @php($p = $item->pencariKerja)
            <tr>
                <td>
                    <a href="{{ route('admin.pendaftar.show', $p) }}" class="font-medium text-brand-700 hover:underline">{{ $p->nama }}</a>
                    <div class="text-xs text-slate-500">{{ $p->kecamatan->nama }} / {{ $p->desa->nama }} · {{ $p->pendidikan->label() }} · {{ $p->umur() }} th</div>
                    @if ($p->no_wa)<a href="https://wa.me/{{ $p->noWaInternasional() }}" target="_blank" rel="noopener" class="text-xs text-green-700 hover:underline">WA {{ $p->no_wa }}</a>@endif
                </td>
                <td class="max-w-sm text-xs">
                    <p><strong>Alasan:</strong> {{ $item->alasan }}</p>
                    @if ($item->pengalaman_bidang)<p class="mt-1"><strong>Pengalaman:</strong> {{ $item->pengalaman_bidang }}</p>@endif
                </td>
                <td>
                    <x-status :status="$item->status" />
                    @if ($item->nomor_sertifikat)
                        <a href="{{ route('berkas.sertifikat', $item) }}" class="mt-1 block text-xs text-brand-600 hover:underline">Sertifikat</a>
                    @endif
                    @if ($item->catatan)<p class="mt-1 text-xs text-slate-500">{{ $item->catatan }}</p>@endif
                </td>
                <td class="min-w-56">
                    @if ($item->status === \App\Enums\StatusPeserta::Mendaftar)
                        <form method="POST" action="{{ route('admin.peserta.seleksi', $item) }}" class="flex flex-col gap-2">
                            @csrf @method('PUT')
                            <input name="catatan" class="form-control" placeholder="Catatan (opsional)">
                            <div class="flex gap-2">
                                <x-button size="sm" variant="success" name="keputusan" value="diterima">Terima</x-button>
                                <x-button size="sm" variant="danger" name="keputusan" value="ditolak">Tolak</x-button>
                            </div>
                        </form>
                    @elseif ($item->status === \App\Enums\StatusPeserta::Diterima)
                        <form method="POST" action="{{ route('admin.peserta.hasil', $item) }}" class="flex flex-col gap-2">
                            @csrf @method('PUT')
                            <input name="catatan" class="form-control" placeholder="Catatan (opsional)">
                            <div class="flex gap-2">
                                <x-button size="sm" variant="success" name="hasil" value="lulus">Lulus</x-button>
                                <x-button size="sm" variant="secondary" name="hasil" value="tidak_lulus">Tidak Lulus</x-button>
                            </div>
                        </form>
                    @else
                        <span class="text-xs text-slate-500">Selesai</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="4"><x-empty message="Belum ada peserta." /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
