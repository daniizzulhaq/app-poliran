{{-- Peta lokasi. Variabel: $konten --}}
<div>
    @if (is_numeric($konten['kontak_latitude']) && is_numeric($konten['kontak_longitude']))
        <x-map-view :lat="$konten['kontak_latitude']" :lng="$konten['kontak_longitude']" height="h-72" :zoom="15" />
    @endif
</div>
