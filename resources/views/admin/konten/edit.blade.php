<x-layouts.app title="Konten Halaman Awal">
    <x-page-header title="Konten Halaman Awal" subtitle="Teks yang tampil di halaman publik.">
        <x-button :href="route('home')" variant="secondary" target="_blank">Lihat Halaman Awal</x-button>
    </x-page-header>

    <form method="POST" action="{{ route('admin.konten.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <x-card title="Hero (bagian paling atas)">
            <div class="space-y-4">
                <x-input label="Judul" name="hero_judul" :value="$konten['hero_judul']" required />
                <x-textarea label="Kalimat pembuka" name="hero_teks" :value="$konten['hero_teks']" required />
            </div>
        </x-card>

        <x-card title="Sambutan Kapolres">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama Kapolres" name="sambutan_nama" :value="$konten['sambutan_nama']" required />
                <x-input label="Pangkat" name="sambutan_pangkat" :value="$konten['sambutan_pangkat']" required />
                <x-textarea label="Isi sambutan" name="sambutan_isi" :value="$konten['sambutan_isi']" rows="6" required class="sm:col-span-2" />
                <div class="sm:col-span-2">
                    @if ($fotoUrl)<img src="{{ $fotoUrl }}" alt="Foto Kapolres" class="mb-2 h-32 rounded-lg object-cover">@endif
                    <x-file-input label="Foto Kapolres" name="sambutan_foto" accept=".jpg,.jpeg,.png,.webp" hint="Gambar maksimal 2 MB. Kosongkan bila tidak diganti." />
                </div>
            </div>
        </x-card>

        <x-card title="Penjelasan Program">
            <x-textarea name="program_teks" :value="$konten['program_teks']" rows="5" required />
        </x-card>

        <x-card title="Kontak">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-textarea label="Alamat" name="kontak_alamat" :value="$konten['kontak_alamat']" required class="sm:col-span-2" />
                <x-input label="Telepon" name="kontak_telepon" :value="$konten['kontak_telepon']" />
                <x-input label="WhatsApp layanan" name="kontak_wa" :value="$konten['kontak_wa']" />
                <x-input label="Email" name="kontak_email" :value="$konten['kontak_email']" />
                <div></div>
                <x-input label="Latitude peta" name="kontak_latitude" :value="$konten['kontak_latitude']" hint="Contoh: -9.8606" />
                <x-input label="Longitude peta" name="kontak_longitude" :value="$konten['kontak_longitude']" hint="Contoh: 124.2836" />
            </div>
        </x-card>

        <x-button size="lg">Simpan Konten</x-button>
    </form>
</x-layouts.app>
