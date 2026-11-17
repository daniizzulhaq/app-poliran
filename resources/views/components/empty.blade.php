@props(['message' => 'Belum ada data.'])
<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-slate-300 bg-white px-4 py-8 text-center text-sm text-slate-500']) }}>
    {{ $message }}
    @if ($slot->isNotEmpty())<div class="mt-3">{{ $slot }}</div>@endif
</div>
