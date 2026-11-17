@props(['label'])
<div {{ $attributes->merge(['class' => 'py-2 sm:grid sm:grid-cols-3 sm:gap-4']) }}>
    <dt class="text-sm font-medium text-slate-500">{{ $label }}</dt>
    <dd class="mt-1 text-sm whitespace-pre-line text-slate-900 sm:col-span-2 sm:mt-0">{{ $slot->isEmpty() ? '-' : $slot }}</dd>
</div>
