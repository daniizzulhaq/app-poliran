@props(['label' => null, 'name', 'options' => [], 'selected' => [], 'required' => false, 'hint' => null])
@php
    $terpilih = array_map('strval', (array) old($name, $selected));
@endphp
<fieldset {{ $attributes }}>
    @if ($label)
        <legend class="form-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</legend>
    @endif
    <div class="grid grid-cols-1 gap-2 rounded-lg border border-slate-200 bg-white p-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($options as $optValue => $optLabel)
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $optValue }}" @checked(in_array((string) $optValue, $terpilih, true))
                    class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                <span>{{ $optLabel }}</span>
            </label>
        @empty
            <p class="text-sm text-slate-500">Belum ada pilihan.</p>
        @endforelse
    </div>
    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    @error($name.'.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</fieldset>
