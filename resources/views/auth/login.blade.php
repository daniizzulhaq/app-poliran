<x-layouts.auth title="Masuk">
    <p class="mb-5 text-sm text-slate-500">Masuk ke akun POLIRAN Anda.</p>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <x-input label="Email" name="email" type="email" required autofocus autocomplete="username" />
        <x-input label="Password" name="password" type="password" required autocomplete="current-password" />
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded border-slate-300"> Ingat saya</label>
            <a href="{{ route('password.request') }}" class="text-brand-600 hover:underline">Lupa password?</a>
        </div>
        <x-button class="w-full">Masuk</x-button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-600">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:underline">Daftar gratis</a></p>
</x-layouts.auth>
