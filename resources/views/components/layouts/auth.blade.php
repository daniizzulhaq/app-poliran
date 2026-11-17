@props(['title' => 'Masuk', 'wide' => false])
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
<body class="min-h-screen bg-gradient-to-b from-brand-900 to-brand-700">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="{{ route('home') }}" class="mb-6"><x-logo dark /></a>
        <div @class(['w-full rounded-2xl bg-white p-6 shadow-xl sm:p-8', 'max-w-md' => ! $wide, 'max-w-2xl' => $wide])>
            <h1 class="mb-1 text-xl font-bold">{{ $title }}</h1>
            <x-flash />
            {{ $slot }}
        </div>
        <a href="{{ route('home') }}" class="mt-6 text-sm text-brand-100 hover:text-white">&larr; Kembali ke halaman awal</a>
    </div>
</body>
</html>
