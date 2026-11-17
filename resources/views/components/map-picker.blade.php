@props(['lat' => null, 'lng' => null, 'label' => 'Lokasi'])
@php
    $lat = old('latitude', $lat);
    $lng = old('longitude', $lng);
@endphp
<div x-data="mapPicker({{ $lat ? (float) $lat : 'null' }}, {{ $lng ? (float) $lng : 'null' }})" {{ $attributes }}>
    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
        <span class="form-label mb-0">{{ $label }} <span class="text-red-600">*</span></span>
        <x-button type="button" variant="secondary" size="sm" @click="ambilLokasi()">📍 Ambil Lokasi Saya</x-button>
    </div>
    <div x-ref="map" class="h-72 w-full rounded-lg border border-slate-300"></div>
    <p class="mt-1 text-xs text-slate-500" x-text="pesan || 'Klik peta atau tekan tombol di atas, lalu geser pin ke titik yang tepat.'"></p>
    <input type="hidden" name="latitude" :value="lat">
    <input type="hidden" name="longitude" :value="lng">
    <p class="mt-1 text-xs text-slate-600" x-show="lat">Koordinat: <span x-text="lat"></span>, <span x-text="lng"></span></p>
    @error('latitude')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
