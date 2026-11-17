@props(['lat', 'lng', 'height' => 'h-64', 'zoom' => 16])
<div x-data="mapView({{ (float) $lat }}, {{ (float) $lng }}, {{ (int) $zoom }})" {{ $attributes }}>
    <div x-ref="map" class="{{ $height }} w-full rounded-lg border border-slate-300"></div>
</div>
