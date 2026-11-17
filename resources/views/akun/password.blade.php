<x-layouts.app title="Ganti Password">
    <x-page-header title="Ganti Password" />
    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('akun.password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <x-input label="Password lama" name="current_password" type="password" required />
            <x-input label="Password baru" name="password" type="password" required hint="Minimal 8 karakter." />
            <x-input label="Ulangi password baru" name="password_confirmation" type="password" required />
            <x-button>Simpan</x-button>
        </form>
    </x-card>
</x-layouts.app>
