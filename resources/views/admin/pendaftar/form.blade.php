<x-layouts.app :title="$pencari ? 'Ubah Pendaftar' : 'Tambah Pendaftar'">
    <x-page-header :title="$pencari ? 'Ubah Pendaftar: '.$pencari->nama : 'Tambah Pendaftar Manual'"
        :back="$pencari ? route('admin.pendaftar.show', $pencari) : route('admin.pendaftar.index')"
        subtitle="Untuk warga yang tidak bisa mendaftar sendiri. Pendaftar yang diinput admin tidak memiliki akun login." />

    <form method="POST" action="{{ $pencari ? route('admin.pendaftar.update', $pencari) : route('admin.pendaftar.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if ($pencari) @method('PUT') @endif
        @include('pencari._form-profil', ['namaDefault' => null, 'waDefault' => null, 'waWajib' => false])
        <x-button size="lg">Simpan</x-button>
    </form>
</x-layouts.app>
