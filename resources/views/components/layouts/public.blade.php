@props(['title' => null, 'description' => 'POLIRAN - Polisi Peduli Pengangguran, program gratis Polres Timor Tengah Selatan untuk pelatihan dan penyaluran kerja warga TTS.'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description }}">
    <title>{{ $title ? $title.' - ' : '' }}POLIRAN Polres TTS</title>
    <link rel="icon" href="{{ asset('images/logo-polres.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white" x-data="{ menu: false }">
    <header class="sticky top-0 z-[1000] border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ route('home') }}"><x-logo /></a>
            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-700 md:flex">
                <a href="{{ route('home') }}" class="hover:text-brand-700">Beranda</a>
                <a href="{{ route('public.pelatihan.index') }}" class="hover:text-brand-700">Pelatihan</a>
                <a href="{{ route('public.tentang') }}" class="hover:text-brand-700">Tentang</a>
                <a href="{{ route('public.kontak') }}" class="hover:text-brand-700">Kontak</a>
            </nav>
            <div class="hidden items-center gap-2 md:flex">
                @auth
                    <x-button :href="route('dashboard')" size="sm">Dashboard</x-button>
                @else
                    <x-button :href="route('login')" variant="secondary" size="sm">Masuk</x-button>
                    <x-button :href="route('register')" size="sm">Daftar</x-button>
                @endauth
            </div>
            <button type="button" class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 md:hidden" @click="menu = !menu" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div x-show="menu" x-cloak class="border-t border-slate-200 bg-white px-4 py-3 md:hidden">
            <nav class="flex flex-col gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="rounded px-2 py-2 hover:bg-slate-50">Beranda</a>
                <a href="{{ route('public.pelatihan.index') }}" class="rounded px-2 py-2 hover:bg-slate-50">Pelatihan</a>
                <a href="{{ route('public.tentang') }}" class="rounded px-2 py-2 hover:bg-slate-50">Tentang</a>
                <a href="{{ route('public.kontak') }}" class="rounded px-2 py-2 hover:bg-slate-50">Kontak</a>
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

    <main class="flex-1">
        @if (session('success') || session('error'))
            <div class="mx-auto max-w-6xl px-4 pt-4"><x-flash /></div>
        @endif
        {{ $slot }}
    </main>

    <footer class="bg-brand-900 text-brand-100">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-3">
            <div>
                <x-logo dark />
                <p class="mt-3 text-sm">Polisi Peduli Pengangguran. Program gratis Polres Timor Tengah Selatan untuk warga TTS.</p>
            </div>
            <div class="text-sm">
                <p class="mb-2 font-semibold text-white">Kontak Layanan</p>
                <p>{{ $kontak['kontak_alamat'] }}</p>
                <p>Telepon: {{ $kontak['kontak_telepon'] }}</p>
                <p>WhatsApp: {{ $kontak['kontak_wa'] }}</p>
                <p>Email: {{ $kontak['kontak_email'] }}</p>
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
