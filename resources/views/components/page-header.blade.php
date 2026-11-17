@props(['title', 'subtitle' => null, 'back' => null])
<div class="mb-5 flex flex-wrap items-start justify-between gap-3">
    <div>
        @if ($back)
            <a href="{{ $back }}" class="mb-1 inline-block text-sm text-brand-600 hover:underline">&larr; Kembali</a>
        @endif
        <h1 class="text-xl font-bold sm:text-2xl">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>@endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap gap-2">{{ $slot }}</div>
    @endif
</div>
