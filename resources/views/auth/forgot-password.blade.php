<x-layouts.auth title="Lupa Password">
    <p class="mb-5 text-sm text-slate-500">Masukkan email akun Anda. Kami kirim tautan untuk membuat password baru.</p>
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-input label="Email" name="email" type="email" required autofocus />
        <x-button class="w-full">Kirim Tautan</x-button>
    </form>
    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="text-brand-700 hover:underline">Kembali ke halaman masuk</a></p>
</x-layouts.auth>
