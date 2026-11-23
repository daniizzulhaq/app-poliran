<x-layouts.public :title="$lowongan->judul">
    <section class="mx-auto max-w-4xl space-y-5 px-4 py-10">
        <a href="{{ route('public.lowongan.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">&larr; Semua Lowongan</a>
        <div>
            <x-badge color="green">Lowongan tersedia</x-badge>
            <h1 class="mt-3 text-3xl font-extrabold">{{ $lowongan->judul }}</h1>
            <p class="mt-2 text-slate-600">{{ $lowongan->lokasi_kerja }} · {{ $lowongan->sisaKuota() }} tempat tersedia</p>
        </div>
        <x-card title="Detail Pekerjaan">
            @include('lowongan._detail')
        </x-card>
        <x-card title="Chat Admin Utama" id="chat-admin">
            <p class="text-sm text-slate-600">Berminat dengan pekerjaan ini? Admin Utama POLIRAN akan membantu memeriksa profil, kemampuan, dan persyaratan pekerjaan bersama Anda melalui chat.</p>
            @guest
                <x-button :href="route('chat.interest', $lowongan)" class="mt-4">Masuk & Chat Admin</x-button>
                <a href="{{ route('register.pencari-kerja') }}" class="mt-3 block text-sm text-brand-700 hover:underline">Belum punya akun? Daftar pencari kerja</a>
            @else
                @if (auth()->user()->isRole(\App\Enums\Role::PencariKerja))
                    @if (auth()->user()->bisaChat())
                    <form method="POST" action="{{ route('chat.start', $lowongan) }}" class="mt-4">
                        @csrf
                        <x-button>Chat Admin</x-button>
                    </form>
                    <p class="mt-2 text-xs text-slate-500">Saat dibuka, pesan minat dikirim ke Admin Utama beserta lowongan ini.</p>
                    @else
                        <p class="mt-4 text-sm font-medium text-brand-700">Lengkapi profil Anda terlebih dahulu agar bisa chat dengan Admin Utama tentang pekerjaan ini.</p>
                        <x-button :href="route('chat.interest', $lowongan)" class="mt-4">Lengkapi Profil untuk Chat</x-button>
                    @endif
                @elseif (auth()->user()->isSuperAdmin())
                    <x-button :href="route('chat.index')" class="mt-4">Buka Chat Pencari Kerja</x-button>
                @else
                    <p class="mt-4 text-sm font-medium">Chat lowongan tersedia untuk akun pencari kerja.</p>
                @endif
            @endguest
        </x-card>
    </section>
</x-layouts.public>
