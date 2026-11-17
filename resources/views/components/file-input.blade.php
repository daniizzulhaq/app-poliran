@props(['label', 'name', 'accept' => null, 'hint' => null, 'required' => false, 'current' => null])
<div {{ $attributes->only('class') }}>
    <label for="f_{{ $name }}" class="form-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</label>
    <input id="f_{{ $name }}" type="file" name="{{ $name }}" @if ($accept) accept="{{ $accept }}" @endif @required($required)
        class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
    @if ($current)<p class="mt-1 text-xs"><a href="{{ $current }}" target="_blank" class="text-brand-600 hover:underline">Lihat berkas saat ini</a> (kosongkan bila tidak diganti)</p>@endif
    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
