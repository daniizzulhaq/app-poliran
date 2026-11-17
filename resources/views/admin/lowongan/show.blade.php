<x-layouts.app :title="$lowongan->judul">
    <x-page-header :title="$lowongan->judul" :subtitle="$lowongan->instansi->nama" :back="route('admin.lowongan.index', ['status' => $lowongan->status->value])">
        @can('tutup', $lowongan)
            <form method="POST" action="{{ route('admin.lowongan.tutup', $lowongan) }}" onsubmit="return confirm('Tutup lowongan ini?')">
                @csrf
                <x-button variant="danger">Tutup Lowongan</x-button>
            </form>
        @endcan
    </x-page-header>

    <div class="mb-4 grid gap-4 sm:grid-cols-4">
        <x-stat label="Status" :value="$lowongan->status->label()" :color="$lowongan->status->color()" />
        <x-stat label="Kuota" :value="$lowongan->kuota" />
        <x-stat label="Tersalur" :value="$lowongan->jumlah_tersalur" color="green" />
        <x-stat label="Sisa kuota" :value="$lowongan->sisaKuota()" color="yellow" />
    </div>

    <div x-data="{ tab: @js($cocok->isNotEmpty() ? 'cocok' : 'detail') }">
        <div class="mb-4 flex gap-1 border-b border-slate-200 text-sm font-medium">
            <button type="button" @click="tab = 'detail'" :class="tab === 'detail' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500'" class="-mb-px border-b-2 px-4 py-2">Detail</button>
            @can('kelola-penyaluran')
                <button type="button" @click="tab = 'cocok'" :class="tab === 'cocok' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500'" class="-mb-px border-b-2 px-4 py-2">Pendaftar Cocok ({{ $cocok->count() }})</button>
                <button type="button" @click="tab = 'penyaluran'" :class="tab === 'penyaluran' ? 'border-brand-700 text-brand-700' : 'border-transparent text-slate-500'" class="-mb-px border-b-2 px-4 py-2">Penyaluran ({{ $penyaluran->count() }})</button>
            @endcan
        </div>

        <div x-show="tab === 'detail'" class="grid gap-4 lg:grid-cols-3">
            <x-card title="Detail Lowongan" class="lg:col-span-2">
                @include('lowongan._detail')
            </x-card>
            <div class="space-y-4">
                <x-card title="Instansi">
                    <dl class="divide-y divide-slate-100">
                        <x-detail label="Nama">{{ $lowongan->instansi->nama }}</x-detail>
                        <x-detail label="Penanggung jawab">{{ $lowongan->instansi->penanggung_jawab }}</x-detail>
                        <x-detail label="WhatsApp">
                            <a href="https://wa.me/{{ \App\Models\PencariKerja::formatWa($lowongan->instansi->no_wa) }}" target="_blank" rel="noopener" class="text-green-700 hover:underline">{{ $lowongan->instansi->no_wa }}</a>
                        </x-detail>
                        <x-detail label="Status instansi"><x-status :status="$lowongan->instansi->status" /></x-detail>
                    </dl>
                    @can('survei-instansi')
                        <a href="{{ route('admin.survei-instansi.show', $lowongan->instansi) }}" class="mt-2 inline-block text-sm text-brand-600 hover:underline">Lihat data verifikasi instansi</a>
                    @endcan
                </x-card>

                @can('survei', $lowongan)
                    <x-card title="Hasil Survei Lowongan">
                        <form method="POST" action="{{ route('admin.survei-lowongan.putusan', $lowongan) }}" class="space-y-4" x-data="{ keputusan: @js(old('keputusan', 'terima')) }">
                            @csrf
                            <ul class="list-disc space-y-1 pl-5 text-xs text-slate-600">
                                <li>Gaji wajar?</li><li>Tidak meminta uang dari pelamar?</li><li>Bukan penipuan?</li>
                            </ul>
                            <x-textarea label="Catatan survei" name="catatan_survei" rows="3" />
                            <div class="space-y-2 text-sm">
                                <label class="flex items-center gap-2"><input type="radio" name="keputusan" value="terima" x-model="keputusan"> ACC / Aktifkan</label>
                                <label class="flex items-center gap-2"><input type="radio" name="keputusan" value="tolak" x-model="keputusan"> Tolak</label>
                            </div>
                            <div x-show="keputusan === 'tolak'" x-cloak>
                                <x-textarea label="Alasan penolakan" name="alasan_tolak" rows="3" />
                            </div>
                            <x-button class="w-full">Simpan Hasil Survei</x-button>
                        </form>
                    </x-card>
                @elseif ($lowongan->disurvei_pada)
                    <x-card title="Hasil Survei">
                        <dl class="divide-y divide-slate-100">
                            <x-detail label="Petugas">{{ $lowongan->surveyor?->name }}</x-detail>
                            <x-detail label="Tanggal">{{ $lowongan->disurvei_pada->format('d/m/Y H:i') }}</x-detail>
                            <x-detail label="Catatan">{{ $lowongan->catatan_survei }}</x-detail>
                            @if ($lowongan->alasan_tolak)<x-detail label="Alasan tolak">{{ $lowongan->alasan_tolak }}</x-detail>@endif
                        </dl>
                    </x-card>
                @endcan
            </div>
        </div>

        @can('kelola-penyaluran')
            <div x-show="tab === 'cocok'" x-cloak>
                @include('admin.lowongan._cocok')
            </div>
            <div x-show="tab === 'penyaluran'" x-cloak>
                @include('admin.penyaluran._tabel', ['daftar' => $penyaluran, 'tampilLowongan' => false])
            </div>
        @endcan
    </div>
</x-layouts.app>
