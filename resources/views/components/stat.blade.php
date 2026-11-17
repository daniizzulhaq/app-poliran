@props(['label', 'value', 'href' => null, 'color' => 'brand'])
@php
    $bar = ['brand' => 'bg-brand-600', 'green' => 'bg-green-500', 'yellow' => 'bg-amber-400', 'red' => 'bg-red-500', 'blue' => 'bg-blue-500', 'purple' => 'bg-purple-500', 'gray' => 'bg-slate-400', 'indigo' => 'bg-indigo-500'][$color] ?? 'bg-brand-600';
@endphp
<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'relative block overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm'.($href ? ' hover:border-brand-300' : '')]) }}>
    <span class="absolute inset-y-0 left-0 w-1 {{ $bar }}"></span>
    <p class="text-xs font-medium text-slate-500 uppercase">{{ $label }}</p>
    <p class="mt-1 text-2xl font-bold text-slate-900">{{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}</p>
</{{ $href ? 'a' : 'div' }}>
