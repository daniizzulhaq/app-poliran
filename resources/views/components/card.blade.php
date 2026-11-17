@props(['title' => null, 'actions' => null, 'padding' => true])
<section {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm']) }}>
    @if ($title || $actions)
        <header class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3">
            @if ($title)<h2 class="text-base font-semibold">{{ $title }}</h2>@endif
            @if ($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endif
        </header>
    @endif
    <div @class(['p-4' => $padding])>{{ $slot }}</div>
</section>
