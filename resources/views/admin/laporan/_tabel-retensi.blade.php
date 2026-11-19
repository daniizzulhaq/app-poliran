{{-- Hasil tindak lanjut pekerja tersalur. Variabel: $retensi (StatistikService::retensi) --}}
<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left text-xs text-slate-600 uppercase">
        <tr>
            <th class="px-4 py-2">Setelah mulai kerja</th>
            <th class="px-4 py-2 text-right">Masih bekerja</th>
            <th class="px-4 py-2 text-right">Berhenti</th>
            <th class="px-4 py-2 text-right">Tidak bisa dihubungi</th>
            <th class="px-4 py-2 text-right">% bertahan</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
        @foreach ($retensi['bulan'] as $r)
            <tr>
                <td class="px-4 py-2">Bulan ke-{{ $r['bulan'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums">{{ $r['masih_bekerja'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums">{{ $r['berhenti'] }}</td>
                <td class="px-4 py-2 text-right tabular-nums">{{ $r['tidak_terhubung'] }}</td>
                <td class="px-4 py-2 text-right font-semibold tabular-nums">{{ $r['persen'] === null ? '-' : $r['persen'].'%' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p class="px-4 py-2 text-xs text-slate-500">
    Total pekerja tersalur: {{ $retensi['tersalur'] }}. % bertahan = masih bekerja ÷ (masih bekerja + berhenti); yang tidak bisa dihubungi tidak dihitung.
</p>
