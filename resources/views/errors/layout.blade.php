{{-- Halaman error mandiri (tanpa query database). Variabel: $kode, $judul, $pesan --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul }} - POLIRAN</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(180deg, #0b1e34, #173a64); color: #0b1e34; padding: 16px; box-sizing: border-box; }
        .kotak { background: #fff; border-radius: 16px; padding: 32px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,.25); }
        .kode { font-size: 56px; font-weight: 800; color: #e0a92a; margin: 8px 0 0; }
        h1 { font-size: 22px; margin: 4px 0 8px; }
        p { color: #475569; line-height: 1.5; }
        .tombol { display: inline-block; margin: 8px 4px 0; padding: 10px 18px; border-radius: 8px; background: #173a64; color: #fff; text-decoration: none; font-weight: 600; font-size: 14px; }
        .tombol.kedua { background: #fff; color: #173a64; border: 1px solid #cbd5e1; }
        img { width: 56px; height: 56px; }
    </style>
</head>
<body>
    <div class="kotak">
        <img src="{{ asset('images/logo-polres.svg') }}" alt="Logo Polres TTS">
        <p class="kode">{{ $kode }}</p>
        <h1>{{ $judul }}</h1>
        @php($pesanKhusus = ($exception ?? null)?->getMessage())
        @php($bawaan = ! $pesanKhusus || ! in_array($kode, ['403', '404'], true) || str_contains($pesanKhusus, 'No query results') || $pesanKhusus === 'This action is unauthorized.' || str_contains($pesanKhusus, 'does not have the right'))
        <p>{{ $bawaan ? $pesan : $pesanKhusus }}</p>
        <a href="{{ url('/') }}" class="tombol">Halaman Awal</a>
        <a href="{{ url('/dashboard') }}" class="tombol kedua">Dashboard</a>
    </div>
</body>
</html>
