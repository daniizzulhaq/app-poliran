<x-layouts.app title="Akun Admin">
    <x-page-header title="Akun Admin" subtitle="Kelola akun admin dan pembagian kecamatan Admin Lapangan.">
        <x-button :href="route('admin.users.create')">+ Tambah Admin</x-button>
    </x-page-header>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email" class="form-control max-w-xs">
        <x-button variant="secondary">Cari</x-button>
    </form>

    <x-table :paginator="$users">
        <x-slot:head>
            <th>Nama</th><th>Email</th><th>Peran</th><th>Kecamatan</th><th>Status</th><th></th>
        </x-slot:head>
        @forelse ($users as $user)
            @php($role = $user->roleEnum())
            <tr>
                <td class="font-medium">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if ($role)<x-badge :color="$role->color()">{{ $role->label() }}</x-badge>@endif
                    @if ($user->permissions->contains('name', 'kelola-pelatihan'))<x-badge color="purple">+ Pelatihan</x-badge>@endif
                </td>
                <td class="text-xs">{{ $user->kecamatan->pluck('nama')->implode(', ') ?: '-' }}</td>
                <td>
                    @if ($user->is_active)<x-badge color="green">Aktif</x-badge>@else<x-badge color="red">Nonaktif</x-badge>@endif
                </td>
                <td class="text-right"><a href="{{ route('admin.users.edit', $user) }}" class="text-brand-600 hover:underline">Ubah</a></td>
            </tr>
        @empty
            <tr><td colspan="6"><x-empty /></td></tr>
        @endforelse
    </x-table>
</x-layouts.app>
