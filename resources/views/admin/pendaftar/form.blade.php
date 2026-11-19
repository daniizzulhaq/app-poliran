<x-layouts.app :title="$pencari ? 'Ubah Pendaftar' : 'Tambah Pendaftar'">
    <x-page-header :title="$pencari ? 'Ubah Pendaftar: '.$pencari->nama : 'Tambah Pendaftar Manual'"
        :back="$pencari ? route('admin.pendaftar.show', $pencari) : route('admin.pendaftar.index')"
        subtitle="Untuk warga yang tidak bisa mendaftar sendiri. Pendaftar yang diinput admin tidak memiliki akun login." />

    <form method="POST" action="{{ $pencari ? route('admin.pendaftar.update', $pencari) : route('admin.pendaftar.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if ($pencari) @method('PUT') @endif
        @include('pencari._form-profil', ['namaDefault' => null, 'waDefault' => null, 'waWajib' => false])

        @if (! $pencari?->persetujuan_pada)
            <x-card title="Persetujuan Warga">
                <p class="mb-3 text-sm text-slate-600">
                    Jelaskan kepada warga bahwa datanya (termasuk NIK dan nomor WhatsApp) disimpan oleh Polres Timor Tengah Selatan
                    dan hanya dipakai untuk program POLIRAN: pelatihan dan penyaluran kerja. Data tidak dibagikan ke pihak lain di luar instansi tujuan penyaluran.
                </p>
                <label class="flex items-start gap-2 text-sm">
                    <input type="checkbox" name="persetujuan" value="1" @checked(old('persetujuan')) @required(! $pencari) class="mt-0.5 rounded border-slate-300">
                    <span>Warga sudah menyetujui datanya disimpan dan digunakan untuk program POLIRAN. @unless ($pencari)<span class="text-red-600">*</span>@endunless</span>
                </label>
                @error('persetujuan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <x-select label="Cara persetujuan diberikan" name="persetujuan_cara" :options="\App\Enums\CaraPersetujuan::opsiPetugas()" class="mt-3 max-w-sm" />
                @if ($pencari)
                    <p class="mt-2 text-xs text-amber-700">Persetujuan pendaftar ini belum tercatat. Centang bila warga sudah menyetujui.</p>
                @endif
            </x-card>
        @endif

        <x-button size="lg">Simpan</x-button>
    </form>
</x-layouts.app>
