<x-layouts.app title="Chat Lowongan">
    <x-page-header title="Chat Lowongan" :subtitle="auth()->user()->isSuperAdmin() ? 'Percakapan pencari kerja dengan Admin Utama. Pilih chat untuk melihat profil dan kecocokan lowongan.' : 'Konsultasikan lowongan yang Anda minati dengan Admin Utama POLIRAN.'" />
    <div x-data="chatInbox(@js($items), @js(route('chat.index', ['page' => $chats->currentPage()])))">
        <p x-show="error" x-cloak x-text="error" class="mb-3 text-sm text-amber-700" role="status"></p>
        <x-card>
            <p x-show="items.length === 0" class="py-8 text-center text-sm text-slate-500">Belum ada percakapan. Pilih lowongan tersedia untuk mulai chat Admin.</p>
            <div class="divide-y divide-slate-100">
                <template x-for="chat in items" :key="chat.id">
                    <a :href="chat.url" class="flex min-w-0 items-start justify-between gap-3 rounded-lg p-4 hover:bg-brand-50">
                        <div class="min-w-0">
                            <p class="font-bold" x-text="chat.name"></p>
                            <p class="mt-1 text-sm font-medium text-brand-700" x-text="chat.job"></p>
                            <p class="mt-1 truncate text-sm text-slate-500" x-text="chat.preview"></p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs text-slate-500" x-text="chat.time"></p>
                            <span x-show="chat.unread > 0" class="mt-2 inline-block rounded-full bg-brand-700 px-2 py-1 text-xs font-bold text-white" x-text="chat.unread + ' baru'"></span>
                        </div>
                    </a>
                </template>
            </div>
        </x-card>
    </div>
    <div class="mt-5">{{ $chats->links() }}</div>
    <x-button :href="route('public.lowongan.index')" variant="secondary" class="mt-4">Lihat Lowongan Tersedia</x-button>
</x-layouts.app>
