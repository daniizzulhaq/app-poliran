@props(['label' => null, 'name', 'value' => null, 'required' => false, 'rows' => 3, 'hint' => null])
@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'f_'.str_replace('.', '_', $key);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->except('class')->merge(['class' => 'form-control'.($errors->has($key) ? ' border-red-500' : '')]) }}>{{ old($key, $value) }}</textarea>
    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @error($key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
