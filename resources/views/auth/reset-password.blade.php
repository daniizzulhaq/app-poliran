<x-layouts.auth title="Buat Password Baru">
    <form method="POST" action="{{ route('password.update') }}" class="mt-4 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-input label="Email" name="email" type="email" :value="$email" required />
        <x-input label="Password baru" name="password" type="password" required autocomplete="new-password" />
        <x-input label="Ulangi password baru" name="password_confirmation" type="password" required autocomplete="new-password" />
        <x-button class="w-full">Simpan Password</x-button>
    </form>
</x-layouts.auth>
