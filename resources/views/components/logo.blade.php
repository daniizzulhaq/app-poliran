@props(['dark' => false])
<span {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <img src="{{ asset('images/logo-polres-tts.png') }}" alt="Logo Polres Timor Tengah Selatan" width="44" height="52" class="h-11 w-10 shrink-0 object-contain">
    <span class="leading-tight">
        <span @class(['block text-xl font-extrabold tracking-[0.12em]', 'text-white' => $dark, 'text-brand-800' => ! $dark])>POLIRAN<span class="text-gold-500">.</span></span>
        <span @class(['block text-[10px] font-medium', 'text-brand-100' => $dark, 'text-slate-500' => ! $dark])>Polres Timor Tengah Selatan</span>
    </span>
</span>
