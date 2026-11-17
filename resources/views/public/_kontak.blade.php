{{-- Bagian kontak. Variabel: $konten --}}
<div class="grid gap-8 lg:grid-cols-2">
    <div>
        <h2 class="text-2xl font-extrabold sm:text-3xl">Kontak Layanan</h2>
        <p class="mt-2 text-slate-600">Ada pertanyaan? Datang langsung atau hubungi petugas kami.</p>
        <dl class="mt-6 space-y-4 text-sm">
            <div class="flex gap-3"><dt class="w-24 shrink-0 font-semibold text-slate-500">Alamat</dt><dd>{{ $konten['kontak_alamat'] }}</dd></div>
            <div class="flex gap-3"><dt class="w-24 shrink-0 font-semibold text-slate-500">Telepon</dt><dd>{{ $konten['kontak_telepon'] }}</dd></div>
            <div class="flex gap-3"><dt class="w-24 shrink-0 font-semibold text-slate-500">WhatsApp</dt>
                <dd>
                    @if (\App\Models\PencariKerja::formatWa($konten['kontak_wa']))
                        <a href="https://wa.me/{{ \App\Models\PencariKerja::formatWa($konten['kontak_wa']) }}" target="_blank" rel="noopener" class="text-green-700 hover:underline">{{ $konten['kontak_wa'] }}</a>
                    @else
                        {{ $konten['kontak_wa'] }}
                    @endif
                </dd>
            </div>
            <div class="flex gap-3"><dt class="w-24 shrink-0 font-semibold text-slate-500">Email</dt><dd>{{ $konten['kontak_email'] }}</dd></div>
        </dl>
    </div>
    @if (is_numeric($konten['kontak_latitude']) && is_numeric($konten['kontak_longitude']))
        <x-map-view :lat="$konten['kontak_latitude']" :lng="$konten['kontak_longitude']" height="h-72" :zoom="15" />
    @endif
</div>
