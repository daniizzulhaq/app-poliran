<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $peserta->pencariKerja->nama }}</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #0b1e34; }
        .bingkai { position: absolute; top: 24px; left: 24px; right: 24px; bottom: 24px; border: 6px double #b9861a; }
        .isi { text-align: center; padding: 70px 80px 0; }
        .kecil { font-size: 13px; letter-spacing: 2px; color: #173a64; }
        h1 { font-size: 46px; margin: 14px 0 4px; letter-spacing: 6px; color: #173a64; }
        .nomor { font-size: 12px; color: #555; }
        .nama { font-size: 34px; font-weight: bold; margin: 26px 0 6px; border-bottom: 2px solid #b9861a; display: inline-block; padding: 0 30px 6px; }
        .teks { font-size: 15px; line-height: 1.6; margin: 10px 60px; }
        .pelatihan { font-size: 22px; font-weight: bold; color: #173a64; }
        .ttd { position: absolute; bottom: 70px; right: 110px; text-align: center; font-size: 13px; }
        .ttd .garis { margin-top: 70px; border-top: 1px solid #0b1e34; padding-top: 4px; width: 240px; }
    </style>
</head>
<body>
    <div class="bingkai"></div>
    <div class="isi">
        <div class="kecil">KEPOLISIAN RESOR TIMOR TENGAH SELATAN · PROGRAM POLIRAN</div>
        <h1>SERTIFIKAT</h1>
        <div class="nomor">Nomor: {{ $peserta->nomor_sertifikat }}</div>
        <div class="teks" style="margin-top: 24px;">Diberikan kepada</div>
        <div class="nama">{{ $peserta->pencariKerja->nama }}</div>
        <div class="teks">
            atas keberhasilannya menyelesaikan pelatihan<br>
            <span class="pelatihan">{{ $peserta->pelatihan->nama }}</span><br>
            yang diselenggarakan oleh {{ $peserta->pelatihan->penyelenggaraLabel() }}<br>
            pada {{ $peserta->pelatihan->tanggal_mulai->translatedFormat('d F Y') }}@if ($peserta->pelatihan->tanggal_selesai && ! $peserta->pelatihan->tanggal_selesai->eq($peserta->pelatihan->tanggal_mulai)) s.d. {{ $peserta->pelatihan->tanggal_selesai->translatedFormat('d F Y') }}@endif
            di Kecamatan {{ $peserta->pelatihan->kecamatan->nama }}, Kabupaten Timor Tengah Selatan.
        </div>
    </div>
    <div class="ttd">
        SoE, {{ ($peserta->pelatihan->tanggal_selesai ?? $peserta->pelatihan->tanggal_mulai)->translatedFormat('d F Y') }}<br>
        Penyelenggara
        <div class="garis">{{ $peserta->pelatihan->penyelenggara }}</div>
    </div>
</body>
</html>
