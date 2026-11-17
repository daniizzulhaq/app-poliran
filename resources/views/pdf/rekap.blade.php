<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap POLIRAN</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin: 0; }
        .sub { color: #555; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 4px 6px; }
        th { background: #173a64; color: #fff; text-align: left; }
        td.angka, th.angka { text-align: right; }
        tr.total td { font-weight: bold; background: #eee; }
        .ringkas td { border: none; padding: 2px 10px 2px 0; }
    </style>
</head>
<body>
    <h1>Rekap Pendaftar POLIRAN - Polres Timor Tengah Selatan</h1>
    <div class="sub">Dicetak {{ now()->translatedFormat('d F Y H:i') }} WITA oleh {{ $dicetakOleh }}</div>

    <table class="ringkas">
        <tr>
            <td>Lowongan aktif: <strong>{{ $ringkasan['lowongan_aktif'] }}</strong></td>
            <td>Instansi terverifikasi: <strong>{{ $ringkasan['instansi_terverifikasi'] }}</strong></td>
            <td>Antrian survei instansi: <strong>{{ $ringkasan['antrian_instansi'] }}</strong></td>
            <td>Antrian survei lowongan: <strong>{{ $ringkasan['antrian_lowongan'] }}</strong></td>
            <td>Pelatihan (dibuka/total): <strong>{{ $ringkasan['pelatihan_dibuka'] }}/{{ $ringkasan['pelatihan_total'] }}</strong></td>
        </tr>
    </table>

    <h3>Per Status</h3>
    <table>
        <tr>@foreach (\App\Enums\StatusPendaftar::cases() as $s)<th class="angka">{{ $s->label() }}</th>@endforeach<th class="angka">Total</th></tr>
        <tr>@foreach (\App\Enums\StatusPendaftar::cases() as $s)<td class="angka">{{ $perStatus[$s->value] }}</td>@endforeach<td class="angka">{{ array_sum($perStatus) }}</td></tr>
    </table>

    <h3>Per Kecamatan</h3>
    <table>
        <tr><th>Kecamatan</th>@foreach (\App\Enums\StatusPendaftar::cases() as $s)<th class="angka">{{ $s->label() }}</th>@endforeach<th class="angka">Total</th></tr>
        @foreach ($rekap as $r)
            <tr><td>{{ $r['kecamatan'] }}</td>@foreach ($r['status'] as $j)<td class="angka">{{ $j }}</td>@endforeach<td class="angka">{{ $r['total'] }}</td></tr>
        @endforeach
        <tr class="total"><td>TOTAL</td>@foreach (\App\Enums\StatusPendaftar::cases() as $s)<td class="angka">{{ $rekap->sum(fn ($r) => $r['status'][$s->value]) }}</td>@endforeach<td class="angka">{{ $rekap->sum('total') }}</td></tr>
    </table>
</body>
</html>
