@php($instansi = $jenis === 'instansi')
<x-layouts.auth :title="$instansi ? 'Daftar sebagai Penyedia Kerja' : 'Daftar sebagai Pencari Kerja'">
    <p class="mb-5 text-sm text-slate-500">
        @if ($instansi)
            Setelah akun dibuat, Anda akan diminta melengkapi data instansi untuk disurvei petugas Polres.
        @else
            Setelah akun dibuat, lengkapi profil Anda agar bisa ikut pelatihan dan disalurkan kerja.
        @endif
    </p>
    <form method="POST" action="{{ $instansi ? route('register.instansi') : route('register.pencari-kerja') }}" class="space-y-4">
        @csrf
        <x-input :label="$instansi ? 'Nama Anda (penanggung jawab akun)' : 'Nama lengkap'" name="name" required autofocus />
        <x-input label="Email" name="email" type="email" required autocomplete="username" hint="Dipakai untuk masuk dan atur ulang password." />
        <x-input label="Nomor WhatsApp" name="phone" type="tel" required placeholder="081234567890" />
        <x-input label="Password" name="password" type="password" required autocomplete="new-password" hint="Minimal 8 karakter." />
        <x-input label="Ulangi password" name="password_confirmation" type="password" required autocomplete="new-password" />
        <label class="flex items-start gap-2 text-sm">
            <input type="checkbox" name="persetujuan" value="1" @checked(old('persetujuan')) class="mt-0.5 rounded border-slate-300" required>
            <span>Saya setuju data saya disimpan dan digunakan oleh Polres Timor Tengah Selatan untuk keperluan program POLIRAN.</span>
        </label>
        @error('persetujuan')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        <x-button class="w-full">Buat Akun</x-button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-600">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Masuk</a></p>
</x-layouts.auth>
