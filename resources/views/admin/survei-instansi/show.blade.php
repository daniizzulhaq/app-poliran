<x-layouts.app :title="'Survei: '.$instansi->nama">
    <x-page-header :title="$instansi->nama" :back="route('admin.survei-instansi.index', ['status' => $instansi->status->value])">
        <x-status :status="$instansi->status" class="text-sm" />
    </x-page-header>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <x-card title="Data Instansi">
                <dl class="divide-y divide-slate-100">
                    <x-detail label="Jenis">{{ $instansi->jenis->label() }}</x-detail>
                    <x-detail label="Penanggung jawab">{{ $instansi->penanggung_jawab }}</x-detail>
                    <x-detail label="No. WhatsApp">
                        {{ $instansi->no_wa }}
                        <a href="https://wa.me/{{ \App\Models\PencariKerja::formatWa($instansi->no_wa) }}" target="_blank" rel="noopener" class="ml-2 text-green-700 hover:underline">Chat WA</a>
                    </x-detail>
                    <x-detail label="Email akun">{{ $instansi->user->email }}</x-detail>
                    <x-detail label="Alamat">{{ $instansi->alamat }}</x-detail>
                    <x-detail label="Dokumen">{{ $instansi->jenis_dokumen->label() }} - {{ $instansi->nomor_dokumen }}</x-detail>
                    <x-detail label="Diajukan">{{ $instansi->diajukan_pada?->format('d/m/Y H:i') }}</x-detail>
                    <x-detail label="Jumlah lowongan">{{ $instansi->job_listings_count }}</x-detail>
                </dl>
            </x-card>

            <x-card title="Dokumen">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="mb-2 text-sm font-medium">{{ $instansi->jenis_dokumen->label() }}</p>
                        <a href="{{ route('berkas.instansi', [$instansi, 'dokumen']) }}" target="_blank" class="block rounded-lg border border-slate-200 p-3 text-center text-sm text-brand-700 hover:bg-brand-50">Buka berkas {{ $instansi->jenis_dokumen->label() }}</a>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium">Selfie memegang KTP</p>
                        <a href="{{ route('berkas.instansi', [$instansi, 'selfie']) }}" target="_blank">
                            <img src="{{ route('berkas.instansi', [$instansi, 'selfie']) }}" alt="Selfie penanggung jawab" class="max-h-64 rounded-lg border border-slate-200 object-contain">
                        </a>
                    </div>
                </div>
            </x-card>

            <x-card title="Lokasi Usaha">
                <x-slot:actions>
                    <x-button :href="$instansi->googleMapsUrl()" variant="secondary" size="sm" target="_blank" rel="noopener">Buka di Google Maps</x-button>
                </x-slot:actions>
                <x-map-view :lat="$instansi->latitude" :lng="$instansi->longitude" height="h-80" />
                <p class="mt-2 text-xs text-slate-500">Koordinat: {{ $instansi->latitude }}, {{ $instansi->longitude }}</p>
            </x-card>
        </div>

        <div class="space-y-4">
            @can('survei', $instansi)
                <x-card title="Hasil Survei">
                    <form method="POST" action="{{ route('admin.survei-instansi.putusan', $instansi) }}" class="space-y-4" x-data="{ keputusan: @js(old('keputusan', 'terima')) }">
                        @csrf
                        <x-textarea label="Catatan survei" name="catatan_survei" rows="4" hint="Contoh: sudah dikunjungi, usaha benar ada." />
                        <div class="space-y-2 text-sm">
                            <label class="flex items-center gap-2"><input type="radio" name="keputusan" value="terima" x-model="keputusan"> ACC / Terverifikasi</label>
                            <label class="flex items-center gap-2"><input type="radio" name="keputusan" value="tolak" x-model="keputusan"> Tolak</label>
                        </div>
                        <div x-show="keputusan === 'tolak'" x-cloak>
                            <x-textarea label="Alasan penolakan" name="alasan_tolak" rows="3" hint="Wajib. Akan dilihat instansi agar bisa diperbaiki." />
                        </div>
                        <x-button class="w-full">Simpan Hasil Survei</x-button>
                    </form>
                </x-card>
            @else
                <x-card title="Hasil Survei">
                    <dl class="divide-y divide-slate-100">
                        <x-detail label="Petugas">{{ $instansi->surveyor?->name }}</x-detail>
                        <x-detail label="Tanggal">{{ $instansi->disurvei_pada?->format('d/m/Y H:i') }}</x-detail>
                        <x-detail label="Catatan">{{ $instansi->catatan_survei }}</x-detail>
                        @if ($instansi->alasan_tolak)<x-detail label="Alasan tolak">{{ $instansi->alasan_tolak }}</x-detail>@endif
                    </dl>
                </x-card>
            @endcan
        </div>
    </div>
</x-layouts.app>
