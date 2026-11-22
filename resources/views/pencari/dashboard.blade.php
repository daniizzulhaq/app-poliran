<x-layouts.app title="Dashboard Saya">
    <x-page-header title="Dashboard Saya" :subtitle="$pencari?->nama" />

    <section class="mb-8">
        @include('lowongan._tersedia')
        <x-button :href="route('chat.index')" variant="secondary" class="mt-4">Chat Saya dengan Admin Utama</x-button>
    </section>

    @if (! $pencari)
        <x-card>
            <p class="font-semibold">Langkah berikutnya: lengkapi profil Anda</p>
            <p class="mt-1 text-sm text-slate-600">Isi data diri, alamat di TTS, minat kerja, dan kemampuan Anda. Setelah itu petugas Polres di kecamatan Anda bisa mendata dan membantu Anda.</p>
            <x-button :href="route('pencari.profil.edit')" class="mt-4">Lengkapi Profil</x-button>
        </x-card>
    @else
        @php($urutan = array_search($pencari->status, $tahapan, true))
        <x-card title="Status Saya" class="mb-4">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-sm text-slate-600">Status saat ini:</span>
                <x-status :status="$pencari->status" class="text-sm" />
            </div>
            <ol class="grid gap-2 sm:grid-cols-6">
                @foreach ($tahapan as $i => $tahap)
                    @php($nonBersertifikat = $tahap === \App\Enums\StatusPendaftar::NonBersertifikat)
                    @php($bersertifikat = $tahap === \App\Enums\StatusPendaftar::Bersertifikat)
                    @php($lewat = $i <= $urutan && ! ($bersertifikat && $pencari->status === \App\Enums\StatusPendaftar::NonBersertifikat) && ! ($nonBersertifikat && $pencari->status !== \App\Enums\StatusPendaftar::NonBersertifikat))
                    <li @class([
                        'rounded-lg border px-3 py-2 text-center text-xs font-medium',
                        'border-brand-600 bg-brand-700 text-white' => $tahap === $pencari->status,
                        'border-brand-200 bg-brand-50 text-brand-700' => $lewat && $tahap !== $pencari->status,
                        'border-slate-200 bg-white text-slate-400' => ! $lewat,
                    ])>{{ $tahap->label() }}</li>
                @endforeach
            </ol>
            <p class="mt-3 text-xs text-slate-500">Alur: Terdaftar &rarr; Direkrut &rarr; Dilatih &rarr; Bersertifikat / Non Bersertifikat &rarr; Disalurkan</p>
        </x-card>

        <div class="grid gap-4 lg:grid-cols-2">
            <x-card title="Pelatihan & Sertifikat">
                <x-slot:actions><x-button :href="route('public.pelatihan.index')" size="sm" variant="secondary">Cari Pelatihan</x-button></x-slot:actions>
                @forelse ($pencari->pesertaPelatihan as $peserta)
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0">
                        <div>
                            <p class="font-medium">{{ $peserta->pelatihan->nama }}</p>
                            <p class="text-xs text-slate-500">{{ $peserta->pelatihan->tanggal_mulai->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-status :status="$peserta->status" />
                            @if ($peserta->nomor_sertifikat)
                                <a href="{{ route('berkas.sertifikat', $peserta) }}" class="text-xs font-semibold text-brand-700 hover:underline">Unduh Sertifikat</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Anda belum mendaftar pelatihan.</p>
                @endforelse
            </x-card>

            <x-card title="Riwayat Status">
                <ol class="relative space-y-3 border-l border-slate-200 pl-4">
                    @foreach ($pencari->riwayatStatus as $r)
                        <li>
                            <span class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full bg-brand-600"></span>
                            <p class="text-sm font-medium">{{ $r->status_baru->label() }}</p>
                            <p class="text-xs text-slate-500">{{ $r->created_at->translatedFormat('d F Y H:i') }}</p>
                            @if ($r->catatan)<p class="text-xs text-slate-600">{{ $r->catatan }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </x-card>
        </div>
    @endif
</x-layouts.app>
