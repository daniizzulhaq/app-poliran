@props(['pelatihan'])
<article {{ $attributes->merge(['class' => 'flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md']) }}>
    <a href="{{ route('public.pelatihan.show', $pelatihan) }}" class="block aspect-[16/9] bg-brand-100">
        @if ($pelatihan->posterUrl())
            <img src="{{ $pelatihan->posterUrl() }}" alt="Poster {{ $pelatihan->nama }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 p-4 text-center text-lg font-bold text-white">{{ $pelatihan->nama }}</div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-4">
        <div class="mb-2 flex flex-wrap gap-1">
            <x-badge color="blue">{{ $pelatihan->kategori->nama }}</x-badge>
            @if ($pelatihan->bersertifikat)<x-badge color="green">Bersertifikat</x-badge>@endif
            <x-badge color="yellow">Gratis</x-badge>
        </div>
        <h3 class="font-bold"><a href="{{ route('public.pelatihan.show', $pelatihan) }}" class="hover:text-brand-700">{{ $pelatihan->nama }}</a></h3>
        <p class="mt-1 text-sm text-slate-600">📅 {{ $pelatihan->tanggal_mulai->translatedFormat('d F Y') }}</p>
        <p class="text-sm text-slate-600">📍 Kec. {{ $pelatihan->kecamatan->nama }}</p>
        <p class="text-xs text-slate-500">Daftar sebelum {{ $pelatihan->batas_pendaftaran->translatedFormat('d F Y') }}</p>
        <div class="mt-auto pt-4">
            <x-button :href="route('pencari.pelatihan.daftar', $pelatihan)" class="w-full">Daftar</x-button>
        </div>
    </div>
</article>
