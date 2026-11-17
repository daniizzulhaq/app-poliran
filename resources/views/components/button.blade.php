@props(['variant' => 'primary', 'href' => null, 'type' => 'submit', 'size' => 'md'])
@php
    $variants = [
        'primary' => 'bg-brand-700 text-white hover:bg-brand-800 focus:ring-brand-200',
        'gold' => 'bg-gold-400 text-brand-900 hover:bg-gold-500 focus:ring-gold-400/40',
        'secondary' => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 focus:ring-slate-200',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-200',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-200',
        'ghost' => 'text-brand-700 hover:bg-brand-50 focus:ring-brand-100',
    ];
    $sizes = ['sm' => 'px-3 py-1.5 text-xs', 'md' => 'px-4 py-2 text-sm', 'lg' => 'px-6 py-3 text-base'];
    $class = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition focus:outline-none focus:ring-4 disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
