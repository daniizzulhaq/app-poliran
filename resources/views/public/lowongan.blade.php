<x-layouts.public title="Lowongan Pekerjaan Tersedia">
    <section class="mx-auto max-w-6xl px-4 py-10">
        @include('lowongan._tersedia')
        <div class="mt-6">{{ $lowongan->links() }}</div>
    </section>
</x-layouts.public>
