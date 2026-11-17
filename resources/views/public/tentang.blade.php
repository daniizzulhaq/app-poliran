<x-layouts.public title="Tentang">
    <section class="bg-brand-900 py-12 text-white">
        <div class="mx-auto max-w-6xl px-4">
            <h1 class="text-3xl font-extrabold text-white">Tentang POLIRAN</h1>
            <p class="mt-2 text-brand-100">Polisi Peduli Pengangguran · Polres Timor Tengah Selatan</p>
        </div>
    </section>
    <section class="mx-auto max-w-6xl px-4 py-12">
        <p class="max-w-3xl text-lg whitespace-pre-line text-slate-700">{{ $konten['program_teks'] }}</p>
        <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
            <strong>Program ini GRATIS.</strong> Petugas tidak pernah meminta uang. Jika ada yang meminta bayaran atas nama POLIRAN, laporkan ke kontak layanan kami.
        </div>
        <h2 class="mt-10 mb-4 text-2xl font-extrabold">Alur Program</h2>
        @include('public._alur')
        <h2 class="mt-10 mb-4 text-2xl font-extrabold">Hasil Sejauh Ini</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            @foreach ([['terdaftar', 'warga terdaftar'], ['dilatih', 'dilatih'], ['bersertifikat', 'bersertifikat'], ['disalurkan', 'disalurkan'], ['instansi', 'instansi terverifikasi']] as [$kunci, $label])
                <x-stat :label="$label" :value="$statistik[$kunci]" />
            @endforeach
        </div>
        <div class="mt-10"><x-button :href="route('register')" size="lg">Daftar Sekarang</x-button></div>
    </section>
</x-layouts.public>
