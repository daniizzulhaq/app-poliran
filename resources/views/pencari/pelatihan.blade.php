<x-layouts.app title="Pelatihan Saya">
    <x-page-header title="Pelatihan Saya">
        <x-button :href="route('public.pelatihan.index')" variant="secondary">Cari Pelatihan</x-button>
    </x-page-header>

    @if (! $pencari)
        <x-empty message="Lengkapi profil Anda sebelum mendaftar pelatihan.">
            <x-button :href="route('pencari.profil.edit')">Lengkapi Profil</x-button>
        </x-empty>
    @else
        <x-table :paginator="$peserta">
            <x-slot:head><th>Pelatihan</th><th>Tanggal & Lokasi</th><th>Status</th><th>Sertifikat</th></x-slot:head>
            @forelse ($peserta as $item)
                <tr>
                    <td class="font-medium"><a href="{{ route('public.pelatihan.show', $item->pelatihan) }}" class="hover:underline">{{ $item->pelatihan->nama }}</a></td>
                    <td class="text-xs">{{ $item->pelatihan->tanggal_mulai->translatedFormat('d F Y') }} · {{ $item->pelatihan->jamLabel() }}<br>{{ $item->pelatihan->alamat }}, Kec. {{ $item->pelatihan->kecamatan->nama }}</td>
                    <td><x-status :status="$item->status" />@if ($item->catatan)<p class="mt-1 text-xs text-slate-500">{{ $item->catatan }}</p>@endif</td>
                    <td>
                        @if ($item->nomor_sertifikat)
                            <x-button :href="route('berkas.sertifikat', $item)" size="sm">Unduh PDF</x-button>
                        @else
                            <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><x-empty message="Anda belum mendaftar pelatihan." /></td></tr>
            @endforelse
        </x-table>
    @endif
</x-layouts.app>
