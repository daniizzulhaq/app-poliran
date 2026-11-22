<x-layouts.app title="Chat Lowongan">
    <x-page-header :title="auth()->user()->isSuperAdmin() ? 'Chat dengan '.$chat->user->name : 'Chat Admin Utama'" :subtitle="$chat->jobListing->judul">
        <x-button :href="route('chat.index')" variant="secondary">Semua Chat</x-button>
    </x-page-header>
    <div class="grid min-w-0 gap-5 xl:grid-cols-3">
        <div class="min-w-0 xl:col-span-2">
            <x-card :padding="false">
                <div class="border-b border-slate-100 p-4">
                    <p class="font-semibold">{{ $chat->jobListing->judul }}</p>
                    <p class="mt-1 text-xs text-slate-500">Percakapan dengan Admin Utama POLIRAN · Pesan baru tampil otomatis</p>
                    @if (! $chat->jobListing->isAktif() || $chat->jobListing->sisaKuota() === 0)
                        <p class="mt-2 text-sm text-amber-700">Lowongan sudah ditutup, kedaluwarsa, atau kuotanya penuh. Percakapan tetap dapat dilanjutkan dengan admin.</p>
                    @endif
                </div>
                <div x-data="jobChat(@js($messages), @js(route('chat.messages', $chat)), @js(route('chat.send', $chat)), @js($hasOlder))">
                    <div x-ref="messages" @scroll="checkBottom()" class="h-[55vh] min-h-72 space-y-3 overflow-y-auto overscroll-contain bg-slate-50 p-4" role="log" aria-label="Pesan percakapan" aria-live="polite">
                        <div x-show="hasOlder" class="text-center">
                            <button type="button" @click="older()" :disabled="loadingOlder" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold disabled:opacity-50" x-text="loadingOlder ? 'Memuat…' : 'Muat pesan sebelumnya'"></button>
                        </div>
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex" :class="message.mine ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[88%] rounded-xl px-4 py-3 shadow-sm" :class="message.mine ? 'bg-brand-700 text-white' : 'border border-slate-200 bg-white text-slate-800'">
                                    <p class="text-xs font-semibold opacity-80" x-text="message.sender"></p>
                                    <p class="mt-1 whitespace-pre-wrap break-words text-sm [overflow-wrap:anywhere]" x-text="message.body"></p>
                                    <p class="mt-2 text-right text-[11px] opacity-70" x-text="message.time"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="border-t border-slate-100 p-4">
                        <button type="button" x-show="newMessages" x-cloak @click="scrollBottom()" class="mb-3 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800">Pesan baru ↓</button>
                        <p x-show="error" x-cloak x-text="error" role="status" class="mb-3 text-sm text-amber-700"></p>
                        <p x-show="sendError" x-cloak x-text="sendError" role="alert" class="mb-3 text-sm text-red-700"></p>
                        <form @submit.prevent="send()" class="space-y-3">
                            <label for="chat-body" class="block text-sm font-medium">Pesan Anda</label>
                            <textarea id="chat-body" x-model="body" :disabled="sending" maxlength="4000" rows="3" required placeholder="Tulis pesan untuk {{ auth()->user()->isSuperAdmin() ? 'pencari kerja' : 'Admin Utama' }}…" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-600 focus:outline-none disabled:opacity-50"></textarea>
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs text-slate-500">Maksimal 4.000 karakter</p>
                                <x-button x-bind:disabled="sending || !body.trim()"><span x-text="sending ? 'Mengirim…' : 'Kirim Pesan'">Kirim Pesan</span></x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="min-w-0 space-y-4">
            @if (auth()->user()->isSuperAdmin())
                <x-card title="Teruskan Rekrutmen">
                    @if ($pencari)
                        <p class="text-sm text-slate-600">Tujuan: <strong>Admin Lapangan Kecamatan {{ $pencari->kecamatan->nama }}</strong>, sesuai domisili pencari kerja.</p>
                        @if ($adminLapangan->isNotEmpty())
                            <p class="mt-2 text-sm">Petugas: {{ $adminLapangan->pluck('name')->implode(', ') }}</p>
                        @else
                            <p class="mt-2 text-sm text-amber-700">Belum ada Admin Lapangan aktif di kecamatan ini. Tetapkan petugas di menu Akun Admin agar rekrutmen dapat ditindaklanjuti.</p>
                        @endif
                        @if ($penyaluran)
                            <div class="mt-3"><x-status :status="$penyaluran->status" /></div>
                            <p class="mt-2 text-xs text-slate-500">Proses sudah tercatat di menu Penyaluran sesuai kecamatan pencari kerja.</p>
                            <x-button :href="route('admin.penyaluran.index', ['status' => $penyaluran->status->value])" variant="secondary" class="mt-3">Lihat Proses Rekrutmen</x-button>
                        @else
                            @can('salurkan', $chat->jobListing)
                                @if ($pencari->status !== \App\Enums\StatusPendaftar::Disalurkan)
                                    <form method="POST" action="{{ route('admin.penyaluran.store', $chat->jobListing) }}" class="mt-4">
                                        @csrf
                                        <input type="hidden" name="pencari_kerja_id" value="{{ $pencari->id }}">
                                        <x-button variant="success" class="w-full">Rekrut & Teruskan ke Admin Lapangan</x-button>
                                    </form>
                                    <p class="mt-2 text-xs text-slate-500">Setelah profil diperiksa, pencari kerja masuk tahap Merekrut. Admin Lapangan wilayahnya melanjutkan proses pelatihan atau penyaluran.</p>
                                @else
                                    <p class="mt-3 text-sm text-slate-500">Pencari kerja sudah disalurkan.</p>
                                @endif
                            @else
                                <p class="mt-3 text-sm text-slate-500">Rekrutmen tersedia saat lowongan aktif dan masih memiliki kuota.</p>
                            @endcan
                        @endif
                    @else
                        <p class="text-sm text-slate-500">Profil pencari kerja belum tersedia.</p>
                    @endif
                </x-card>
                <x-card title="Detail Pencari Kerja">
                    @if ($pencari)
                        <dl class="divide-y divide-slate-100 text-sm">
                            <x-detail label="Nama">{{ $pencari->nama }}</x-detail>
                            <x-detail label="NIK"><span class="font-mono">{{ $pencari->nik }}</span></x-detail>
                            <x-detail label="Pendidikan">{{ $pencari->pendidikan->label() }} {{ $pencari->jurusan }}</x-detail>
                            <x-detail label="Umur">{{ $pencari->umur() }} tahun</x-detail>
                            <x-detail label="Domisili">{{ $pencari->desa->nama }}, {{ $pencari->kecamatan->nama }}</x-detail>
                            <x-detail label="Minat kerja">{{ $pencari->kategori->pluck('nama')->implode(', ') ?: 'Belum diisi' }}</x-detail>
                            <x-detail label="Kemampuan">{{ $pencari->skills->pluck('nama')->implode(', ') ?: 'Belum diisi' }}</x-detail>
                            <x-detail label="Kemampuan lain">{{ $pencari->kemampuan_lain }}</x-detail>
                            <x-detail label="Pengalaman">{{ $pencari->pengalaman_kerja }}</x-detail>
                            <x-detail label="Status"><x-status :status="$pencari->status" /></x-detail>
                        </dl>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-button :href="route('admin.pendaftar.show', $pencari)" size="sm">Lihat Detail Lengkap</x-button>
                            @if ($pencari->cv_path)
                                <x-button :href="route('berkas.cv', $pencari)" variant="secondary" size="sm">Lihat CV</x-button>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-slate-500">Profil pencari kerja belum tersedia.</p>
                    @endif
                </x-card>
                <x-card title="Kecocokan dengan Lowongan">
                    @if ($rincian)
                        <p class="text-3xl font-extrabold text-brand-700">{{ $rincian['skor'] }}<span class="text-base text-slate-500"> / 100</span></p>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div>Minat / pelatihan: <strong>{{ $rincian['kategori'] ? 'Sesuai' : 'Belum sesuai' }}</strong></div>
                            <div>Kemampuan: <strong>{{ $rincian['skill_diminta'] ? $rincian['skill_sama'].' dari '.$rincian['skill_diminta'].' sesuai' : 'Tanpa syarat khusus' }}</strong></div>
                            <div>Pendidikan: <strong>{{ $rincian['pendidikan'] ? 'Memenuhi' : 'Belum memenuhi' }}</strong></div>
                            <div>Umur: <strong>{{ $rincian['umur'] ? 'Memenuhi' : 'Di luar rentang' }}</strong></div>
                        </dl>
                        @php($kurang = $chat->jobListing->skills->diff($pencari->skills)->pluck('nama')->implode(', '))
                        @if ($kurang)<p class="mt-3 text-sm text-amber-700">Kemampuan yang belum tercatat: {{ $kurang }}</p>@endif
                    @else
                        <p class="text-sm text-amber-700">{{ ! $pencari ? 'Profil belum tersedia.' : ($pencari->status === \App\Enums\StatusPendaftar::Disalurkan ? 'Pencari kerja sudah berstatus disalurkan.' : 'Persyaratan jenis kelamin belum sesuai.') }}</p>
                    @endif
                    <p class="mt-3 text-xs text-slate-500">Skor adalah panduan awal. Periksa pengalaman dan seluruh persyaratan bersama pencari kerja sebelum memutuskan penyaluran.</p>
                </x-card>
            @else
                <x-card title="Profil untuk Penilaian">
                    <p class="text-sm text-slate-600">Admin Utama dapat melihat profil, kemampuan, pengalaman, dan CV Anda untuk membantu menilai kecocokan dengan pekerjaan ini.</p>
                    <x-button :href="route('pencari.profil.edit')" variant="secondary" class="mt-4">Perbarui Profil & Kemampuan</x-button>
                </x-card>
            @endif
            <x-card title="Persyaratan Pekerjaan">
                @include('lowongan._detail', ['lowongan' => $chat->jobListing])
                @if (auth()->user()->isSuperAdmin())
                    <x-button :href="route('admin.lowongan.show', $chat->jobListing)" variant="secondary" class="mt-4">Kelola Lowongan & Penyaluran</x-button>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.app>
