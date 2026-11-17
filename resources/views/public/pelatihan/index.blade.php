<x-layouts.public title="Pelatihan">
    <section class="bg-brand-900 py-12 text-white">
        <div class="mx-auto max-w-6xl px-4">
            <h1 class="text-3xl font-extrabold text-white">Pelatihan yang Sedang Dibuka</h1>
            <p class="mt-2 max-w-2xl text-brand-100">Pelatihan diselenggarakan Polres Timor Tengah Selatan bersama mitra. Gratis untuk warga TTS. Daftar dengan akun pencari kerja.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10">
        @if ($kategori->isNotEmpty())
            <div class="mb-6 flex flex-wrap gap-2">
                <a href="{{ route('public.pelatihan.index') }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-brand-700 text-white' => ! request('kategori'), 'bg-slate-100' => request('kategori')])>Semua</a>
                @foreach ($kategori as $slug => $nama)
                    <a href="{{ route('public.pelatihan.index', ['kategori' => $slug]) }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-brand-700 text-white' => request('kategori') === $slug, 'bg-slate-100' => request('kategori') !== $slug])>{{ $nama }}</a>
                @endforeach
            </div>
        @endif

        @if ($pelatihan->isEmpty())
            <x-empty message="Belum ada pelatihan yang dibuka. Silakan cek kembali nanti." />
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pelatihan as $item)
                    <x-pelatihan-card :pelatihan="$item" />
                @endforeach
            </div>
            <div class="mt-6">{{ $pelatihan->links() }}</div>
        @endif
    </section>
</x-layouts.public>
