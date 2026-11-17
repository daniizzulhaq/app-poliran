{{-- Tabel rekap per kecamatan x status. Variabel: $rekap --}}
<table class="table-base">
    <thead>
        <tr>
            <th>Kecamatan</th>
            @foreach (\App\Enums\StatusPendaftar::cases() as $s)<th class="text-right">{{ $s->label() }}</th>@endforeach
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
        @forelse ($rekap as $r)
            <tr>
                <td class="font-medium">{{ $r['kecamatan'] }}</td>
                @foreach ($r['status'] as $jumlah)<td class="text-right">{{ $jumlah }}</td>@endforeach
                <td class="text-right font-bold">{{ $r['total'] }}</td>
            </tr>
        @empty
            <tr><td colspan="8">Belum ada data.</td></tr>
        @endforelse
        <tr class="bg-slate-50 font-bold">
            <td>TOTAL</td>
            @foreach (\App\Enums\StatusPendaftar::cases() as $s)<td class="text-right">{{ $rekap->sum(fn ($r) => $r['status'][$s->value]) }}</td>@endforeach
            <td class="text-right">{{ $rekap->sum('total') }}</td>
        </tr>
    </tbody>
</table>
