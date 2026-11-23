<x-layouts.app title="Profil & CV">
    <x-page-header title="Profil & CV" subtitle="Lengkapi isian wajib profil untuk membuka chat dengan Admin Utama dan membantu petugas mencarikan pelatihan serta pekerjaan yang cocok." />

    <form method="POST" action="{{ route('pencari.profil.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        @include('pencari._form-profil', ['namaDefault' => $user->name, 'waDefault' => $user->phone, 'tanggalLahirDefault' => $user->tanggal_lahir?->format('Y-m-d')])
        <x-button size="lg">Simpan Profil</x-button>
    </form>
</x-layouts.app>
