@props(['dark' => false])
<span {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <img src="{{ asset('images/logo-polres.svg') }}" alt="Logo Polres Timor Tengah Selatan" class="h-9 w-9 shrink-0">
    <span class="leading-tight">
        <span @class(['block text-base font-extrabold tracking-wide', 'text-white' => $dark, 'text-brand-800' => ! $dark])>POLIRAN</span>
        <span @class(['block text-[10px] font-medium', 'text-brand-100' => $dark, 'text-slate-500' => ! $dark])>Polres Timor Tengah Selatan</span>
    </span>
</span>
