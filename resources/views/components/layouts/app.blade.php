@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - POLIRAN</title>
    <link rel="icon" href="{{ asset('images/logo-polres.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" x-data="{ sidebar: false }">
    {{-- Sidebar --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-brand-900 text-brand-100 transition-transform lg:translate-x-0">
        <div class="flex h-16 items-center border-b border-white/10 px-4">
            <a href="{{ route('home') }}"><x-logo dark /></a>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm">
            @foreach ($menu as $grup => $items)
                <p class="mt-4 mb-1 px-2 text-[11px] font-semibold tracking-wider text-brand-200/70 uppercase first:mt-0">{{ $grup }}</p>
                @foreach ($items as $item)
                    <a href="{{ $item['url'] }}" @class([
                        'block rounded-lg px-3 py-2 font-medium transition',
                        'bg-white/10 text-white' => $item['active'],
                        'hover:bg-white/5 hover:text-white' => ! $item['active'],
                    ])>{{ $item['label'] }}</a>
                @endforeach
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-4 text-xs">
            <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
            <p class="text-brand-200">{{ $roleLabel }}</p>
        </div>
    </aside>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur">
            <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebar = true" aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <p class="truncate text-sm font-semibold text-slate-700">{{ $title }}</p>
            <div class="flex items-center gap-2">
                @if (auth()->user()->bisaChat())
                    <a href="{{ route('chat.index') }}" x-data="chatBadge(@js(route('chat.unread')))" class="flex items-center gap-1 rounded-lg px-2 py-2 text-sm font-semibold text-brand-700" aria-label="Buka chat lowongan">
                        Chat <span x-show="count > 0" x-cloak x-text="count > 99 ? '99+' : count" class="rounded-full bg-red-600 px-2 py-0.5 text-xs text-white"></span>
                    </a>
                @endif
                <a href="{{ route('akun.password.edit') }}" class="hidden text-sm text-slate-600 hover:text-brand-700 sm:inline">Ganti Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="secondary" size="sm">Keluar</x-button>
                </form>
            </div>
        </header>
        <main class="mx-auto max-w-7xl px-4 py-6">
            <x-flash />
            {{ $slot }}
        </main>
    </div>
</body>
</html>
