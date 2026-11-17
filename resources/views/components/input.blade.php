@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])
@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'f_'.str_replace('.', '_', $key);
    $nilai = $type === 'password' ? null : old($key, $value);
@endphp
<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $nilai }}" @required($required)
        {{ $attributes->except('class')->merge(['class' => 'form-control'.($errors->has($key) ? ' border-red-500' : '')]) }}>
    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @error($key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
