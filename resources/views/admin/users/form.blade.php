@php($baru = ! $user->exists)
<x-layouts.app :title="$baru ? 'Tambah Admin' : 'Ubah Admin'">
    <x-page-header :title="$baru ? 'Tambah Admin' : 'Ubah Admin: '.$user->name" :back="route('admin.users.index')" />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ $baru ? route('admin.users.store') : route('admin.users.update', $user) }}" class="space-y-4"
            x-data="{ role: @js(old('role', $user->roleEnum()?->value ?? '')) }">
            @csrf
            @unless ($baru) @method('PUT') @endunless

            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama" name="name" :value="$user->name" required />
                <x-input label="Email" name="email" type="email" :value="$user->email" required />
                <x-input label="No. WhatsApp" name="phone" :value="$user->phone" />
                <x-select label="Peran" name="role" :options="$roles" :value="$user->roleEnum()?->value" required x-model="role" />
            </div>

            <div x-show="role === 'admin_lapangan'" x-cloak class="space-y-3 rounded-lg bg-brand-50 p-4">
                <x-checkboxes label="Kecamatan yang ditugaskan" name="kecamatan" :options="$kecamatan"
                    :selected="$user->exists ? $user->kecamatan->pluck('id')->all() : []" hint="Admin Lapangan hanya melihat pendaftar dari kecamatan ini." />
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="boleh_kelola_pelatihan" value="0">
                    <input type="checkbox" name="boleh_kelola_pelatihan" value="1" class="rounded border-slate-300"
                        @checked(old('boleh_kelola_pelatihan', $user->exists && $user->permissions->contains('name', 'kelola-pelatihan')))>
                    Boleh membuat & mengelola pelatihan
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-input :label="$baru ? 'Password' : 'Password baru (kosongkan bila tidak diganti)'" name="password" type="password" :required="$baru" autocomplete="new-password" />
                <x-input label="Ulangi password" name="password_confirmation" type="password" autocomplete="new-password" />
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $user->is_active))>
                Akun aktif
            </label>

            <x-button>Simpan</x-button>
        </form>
    </x-card>
</x-layouts.app>
