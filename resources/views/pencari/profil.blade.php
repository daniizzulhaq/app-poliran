<x-layouts.app title="Profil & CV">
    <x-page-header title="Profil & CV" subtitle="Lengkapi data Anda agar petugas bisa mencarikan pelatihan dan pekerjaan yang cocok." />

    <form method="POST" action="{{ route('pencari.profil.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        @include('pencari._form-profil', ['namaDefault' => $user->name, 'waDefault' => $user->phone])
        <x-button size="lg">Simpan Profil</x-button>
    </form>
</x-layouts.app>
