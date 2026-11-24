@props(['title' => null, 'description' => 'POLIRAN - Polisi Peduli Pengangguran, program gratis Polres Timor Tengah Selatan untuk pelatihan dan penyaluran kerja warga TTS.'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description }}">
    <title>{{ $title ? $title.' - ' : '' }}POLIRAN Polres TTS</title>
    <meta name="theme-color" content="#0b1e34">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-polres-tts.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-site flex min-h-screen flex-col bg-white" x-data="{ menu: false }" @keydown.escape.window="menu = false">
    <a href="#konten-utama" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[1100] focus:rounded-lg focus:bg-white focus:p-3">Lewati ke konten utama</a>
    <div class="bg-brand-900 text-brand-100">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-2 text-[10px] sm:text-xs">
            <p class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold-400" aria-hidden="true"></span> Layanan Polres Timor Tengah Selatan</p>
            <p class="shrink-0 font-semibold text-gold-400">Gratis &amp; tanpa biaya</p>
        </div>
    </div>
    <header class="sticky top-0 z-[1000] border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-20 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ route('home') }}" aria-label="POLIRAN — Beranda"><x-logo /></a>
            <nav aria-label="Navigasi utama" class="public-nav hidden items-center gap-6 text-sm font-medium text-slate-700 md:flex">
                <a href="{{ route('home') }}" aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}" class="hover:text-brand-700">Beranda</a>
                <a href="{{ route('public.lowongan.index') }}" aria-current="{{ request()->routeIs('public.lowongan.*') ? 'page' : 'false' }}" class="hover:text-brand-700">Lowongan</a>
                <a href="{{ route('public.pelatihan.index') }}" aria-current="{{ request()->routeIs('public.pelatihan.*') ? 'page' : 'false' }}" class="hover:text-brand-700">Pelatihan</a>
                <a href="{{ route('public.tentang') }}" aria-current="{{ request()->routeIs('public.tentang') ? 'page' : 'false' }}" class="hover:text-brand-700">Tentang</a>
                <a href="{{ route('public.kontak') }}" aria-current="{{ request()->routeIs('public.kontak') ? 'page' : 'false' }}" class="hover:text-brand-700">Lokasi</a>
            </nav>
            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <x-button :href="route('dashboard')" size="sm">Dashboard</x-button>
                @else
                    <x-button :href="route('login')" variant="secondary" size="sm">Masuk</x-button>
                    <x-button :href="route('register')" size="sm">Daftar <span aria-hidden="true">&rarr;</span></x-button>
                @endauth
            </div>
            <button type="button" class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 md:hidden" @click="menu = !menu" :aria-expanded="menu.toString()" aria-controls="menu-ponsel" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div id="menu-ponsel" x-show="menu" x-cloak class="border-t border-slate-200 bg-white px-4 py-3 md:hidden">
            <nav aria-label="Navigasi ponsel" class="flex flex-col gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="rounded px-2 py-2 hover:bg-slate-50">Beranda</a>
                <a href="{{ route('public.lowongan.index') }}" class="rounded px-2 py-2 hover:bg-slate-50">Lowongan</a>
                <a href="{{ route('public.pelatihan.index') }}" class="rounded px-2 py-2 hover:bg-slate-50">Pelatihan</a>
                <a href="{{ route('public.tentang') }}" class="rounded px-2 py-2 hover:bg-slate-50">Tentang</a>
                <a href="{{ route('public.kontak') }}" class="rounded px-2 py-2 hover:bg-slate-50">Lokasi</a>
            </nav>
            <div class="mt-3 grid grid-cols-2 gap-2">
                @auth
                    <x-button :href="route('dashboard')" class="col-span-2">Dashboard</x-button>
                @else
                    <x-button :href="route('login')" variant="secondary">Masuk</x-button>
                    <x-button :href="route('register')">Daftar</x-button>
                @endauth
            </div>
        </div>
    </header>

    <main id="konten-utama" class="flex-1">
        @if (session('success') || session('error'))
            <div class="mx-auto max-w-6xl px-4 pt-4"><x-flash /></div>
        @endif
        {{ $slot }}
    </main>

    <footer class="border-t-4 border-gold-400 bg-brand-900 text-brand-100">
        <div class="mx-auto grid max-w-6xl items-start gap-10 px-4 py-14 md:grid-cols-[2fr_1fr]">
            <div>
                <x-logo dark />
                <p class="mt-3 text-sm">Polisi Peduli Pengangguran. Program gratis Polres Timor Tengah Selatan untuk warga TTS.</p>
                <div class="mt-5 inline-flex rounded-xl bg-white px-4 py-3">
                    <img src="{{ asset('images/logo-polisi-bersama-masyarakat.png') }}" alt="Polisi Bersama Masyarakat — Melindungi, Mengayomi dan Melayani" width="240" height="88" class="h-16 w-auto max-w-full object-contain" loading="lazy">
                </div>
            </div>
            <div class="text-sm">
                <p class="mb-2 font-semibold text-white">Ayo bergabung</p>
                <p class="mb-3">Daftar sekarang, gratis tanpa biaya apa pun.</p>
                <x-button :href="route('register')" variant="gold">Daftar Sekarang</x-button>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-brand-200">&copy; {{ date('Y') }} Polres Timor Tengah Selatan. POLIRAN.</div>
    </footer>
</body>
</html>
