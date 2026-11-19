<x-layouts.app :title="$pencari->nama">
    <x-page-header :title="$pencari->nama" :back="route('admin.pendaftar.index')">
        <x-button :href="route('admin.pendaftar.edit', $pencari)" variant="secondary">Ubah Data</x-button>
    </x-page-header>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <x-card title="Data Pendaftar">
                <dl class="divide-y divide-slate-100">
                    <x-detail label="Status"><x-status :status="$pencari->status" /></x-detail>
                    <x-detail label="NIK"><span class="font-mono">{{ $pencari->nikTersensor() }}</span></x-detail>
                    <x-detail label="Jenis kelamin">{{ $pencari->jenis_kelamin->label() }}</x-detail>
                    <x-detail label="Tanggal lahir">{{ $pencari->tanggal_lahir->translatedFormat('d F Y') }} ({{ $pencari->umur() }} tahun)</x-detail>
                    <x-detail label="Pendidikan">{{ $pencari->pendidikan->label() }}{{ $pencari->jurusan ? ' - '.$pencari->jurusan : '' }}</x-detail>
                    <x-detail label="Alamat">{{ $pencari->alamat_detail }}, Desa {{ $pencari->desa->nama }}, Kec. {{ $pencari->kecamatan->nama }}, Kab. Timor Tengah Selatan</x-detail>
                    <x-detail label="No. WhatsApp">
                        @if ($pencari->no_wa)
                            {{ $pencari->no_wa }}
                            <a href="https://wa.me/{{ $pencari->noWaInternasional() }}" target="_blank" rel="noopener" class="ml-2 text-green-700 hover:underline">Chat WA</a>
                            <a href="tel:{{ $pencari->no_wa }}" class="ml-2 text-brand-600 hover:underline">Telepon</a>
                        @endif
                    </x-detail>
                    <x-detail label="Persetujuan data">
                        @if ($pencari->persetujuan_pada)
                            {{ $pencari->persetujuan_cara->label() }} · {{ $pencari->persetujuan_pada->format('d/m/Y') }}
                        @else
                            <span class="text-amber-700">Belum tercatat.</span>
                            @can('update', $pencari)<a href="{{ route('admin.pendaftar.edit', $pencari) }}" class="ml-1 text-brand-600 hover:underline">Catat persetujuan</a>@endcan
                        @endif
                    </x-detail>
                    <x-detail label="Kategori minat">{{ $pencari->kategori->pluck('nama')->implode(', ') }}</x-detail>
                    <x-detail label="Skill">{{ $pencari->skills->pluck('nama')->implode(', ') }}</x-detail>
                    <x-detail label="Kemampuan lain">{{ $pencari->kemampuan_lain }}</x-detail>
                    <x-detail label="Pengalaman kerja">{{ $pencari->pengalaman_kerja }}</x-detail>
                    <x-detail label="CV">
                        @if ($pencari->cv_path)<a href="{{ route('berkas.cv', $pencari) }}" target="_blank" class="text-brand-600 hover:underline">Buka CV (PDF)</a>@endif
                    </x-detail>
                    <x-detail label="Akun">{{ $pencari->user?->email ?? 'Tanpa akun (diinput '.($pencari->pembuat?->name ?? 'admin').')' }}</x-detail>
                </dl>
            </x-card>

            @can('kelola-penyaluran')
                <x-card title="Lowongan Cocok">
                    @if ($pencari->status === \App\Enums\StatusPendaftar::Disalurkan)
                        <p class="text-sm text-slate-500">Pendaftar sudah disalurkan, tidak ikut pencocokan.</p>
                    @else
                        @forelse ($lowonganCocok as $row)
                            @php($l = $row['lowongan'])
                            @php($r = $row['rincian'])
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0">
                                <div>
                                    <a href="{{ route('admin.lowongan.show', $l) }}" class="font-medium text-brand-700 hover:underline">{{ $l->judul }}</a>
                                    <p class="text-xs text-slate-500">{{ $l->instansi->nama }} · {{ $l->kategori->nama }} · sisa kuota {{ $l->sisaKuota() }}</p>
                                    <p class="text-xs">
                                        kategori {{ $r['kategori'] ? '✓' : '✗' }} · skill {{ $r['skill_sama'] }}/{{ $r['skill_diminta'] }} ·
                                        pendidikan {{ $r['pendidikan'] ? '✓' : '✗' }} · umur {{ $r['umur'] ? '✓' : '✗' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-bold">{{ $r['skor'] }}</span>
                                    @if ($pencari->penyaluran->doesntContain('job_listing_id', $l->id))
                                        @can('salurkan', $pencari)
                                            <form method="POST" action="{{ route('admin.penyaluran.store', $l) }}">
                                                @csrf
                                                <input type="hidden" name="pencari_kerja_id" value="{{ $pencari->id }}">
                                                <x-button size="sm" variant="success">Salurkan</x-button>
                                            </form>
                                        @endcan
                                    @else
                                        <x-badge color="blue">Sudah disalurkan</x-badge>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada lowongan aktif yang cocok.</p>
                        @endforelse
                    @endif
                </x-card>

                <x-card title="Riwayat Penyaluran">
                    @forelse ($pencari->penyaluran as $item)
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0">
                            <a href="{{ route('admin.lowongan.show', $item->job_listing_id) }}" class="text-brand-700 hover:underline">{{ $item->jobListing->judul }}</a>
                            <span class="flex items-center gap-2 text-xs text-slate-500">{{ $item->created_at->format('d/m/Y') }} <x-status :status="$item->status" /></span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum pernah disalurkan.</p>
                    @endforelse
                </x-card>
            @endcan

            <x-card title="Pelatihan">
                @forelse ($pencari->pesertaPelatihan as $peserta)
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 py-2 text-sm last:border-0">
                        <span>{{ $peserta->pelatihan->nama }}</span>
                        <x-status :status="$peserta->status" />
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum mengikuti pelatihan.</p>
                @endforelse
            </x-card>
        </div>

        <div class="space-y-4">
            @can('ubahStatus', $pencari)
                <x-card title="Ubah Status">
                    <form method="POST" action="{{ route('admin.pendaftar.status', $pencari) }}" class="space-y-3">
                        @csrf
                        <x-select label="Status baru" name="status" :options="$statusOptions" :value="$pencari->status" required :placeholder="false" />
                        <x-textarea label="Catatan" name="catatan" rows="3" required />
                        <x-button class="w-full">Simpan Status</x-button>
                    </form>
                </x-card>
            @endcan

            <x-card title="Riwayat Status">
                <ol class="relative space-y-4 border-l border-slate-200 pl-4">
                    @foreach ($pencari->riwayatStatus as $r)
                        <li>
                            <span class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full bg-brand-600"></span>
                            <p class="text-sm">
                                @if ($r->status_lama){{ $r->status_lama->label() }} &rarr; @endif<strong>{{ $r->status_baru->label() }}</strong>
                            </p>
                            <p class="text-xs text-slate-500">{{ $r->created_at->format('d/m/Y H:i') }} · {{ $r->user?->name ?? 'Sistem' }}</p>
                            @if ($r->catatan)<p class="mt-1 text-xs text-slate-600">{{ $r->catatan }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </x-card>
        </div>
    </div>
</x-layouts.app>
