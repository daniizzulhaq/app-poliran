@props(['name'])
@php
    $paths = [
        'shield' => 'M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z M8 12l3 3 5-6',
        'check' => 'M20 6 9 17l-5-5',
        'briefcase' => 'M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2 M3 6h18v14H3Z M3 11a20 20 0 0 0 18 0 M12 10v4',
        'book' => 'M12 5C8 2 4 3 2 4v16c4-2 7-1 10 1 3-2 6-3 10-1V4c-2-1-6-2-10 1Z M12 5v16',
        'building' => 'M4 22V3h12v19 M16 9h4v13 M8 7h4 M8 11h4 M8 15h4 M8 22v-3h4v3 M2 22h20',
    ];
@endphp
<svg {{ $attributes->class(['h-6 w-6 shrink-0']) }} fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['check'] }}" /></svg>
