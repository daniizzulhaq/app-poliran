{{-- Tab "Pendaftar Cocok". Variabel: $lowongan, $cocok --}}
@php($ikon = fn (bool $ok) => $ok ? '✓' : '✗')
@if (! $lowongan->isAktif())
    <x-empty message="Pencocokan hanya untuk lowongan aktif." />
@else
    <p class="mb-3 text-sm text-slate-600">Urut dari skor tertinggi. Skor: kategori 40 + skill 40 + pendidikan 10 + umur 10. Pendaftar berstatus disalurkan tidak ditampilkan.</p>
    <x-table>
        <x-slot:head><th>Pendaftar</th><th>Kecamatan</th><th>Skor</th><th>Rincian</th><th></th></x-slot:head>
        @forelse ($cocok as $row)
            @php($p = $row['pencari'])
            @php($r = $row['rincian'])
            <tr>
                <td>
                    <a href="{{ route('admin.pendaftar.show', $p) }}" class="font-medium text-brand-700 hover:underline">{{ $p->nama }}</a>
                    <div class="text-xs text-slate-500">{{ $p->pendidikan->label() }} · {{ $p->umur() }} th · <x-status :status="$p->status" /></div>
                </td>
                <td>{{ $p->kecamatan->nama }}</td>
                <td><span class="text-lg font-bold {{ $r['skor'] >= 70 ? 'text-green-700' : ($r['skor'] >= 40 ? 'text-amber-600' : 'text-slate-500') }}">{{ $r['skor'] }}</span></td>
                <td class="text-xs whitespace-nowrap">
                    <span @class(['text-green-700' => $r['kategori'], 'text-red-600' => ! $r['kategori']])>kategori {{ $ikon($r['kategori']) }}</span> ·
                    <span>skill {{ $r['skill_sama'] }}/{{ $r['skill_diminta'] }}</span> ·
                    <span @class(['text-green-700' => $r['pendidikan'], 'text-red-600' => ! $r['pendidikan']])>pendidikan {{ $ikon($r['pendidikan']) }}</span> ·
                    <span @class(['text-green-700' => $r['umur'], 'text-red-600' => ! $r['umur']])>umur {{ $ikon($r['umur']) }}</span>
                </td>
                <td class="text-right">
                    @can('salurkan', $lowongan)
                        <form method="POST" action="{{ route('admin.penyaluran.store', $lowongan) }}">
                            @csrf
                            <input type="hidden" name="pencari_kerja_id" value="{{ $p->id }}">
                            <x-button size="sm" variant="success">Salurkan ke lowongan ini</x-button>
                        </form>
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td colspan="5"><x-empty message="Belum ada pendaftar yang cocok." /></td></tr>
        @endforelse
    </x-table>
@endif
