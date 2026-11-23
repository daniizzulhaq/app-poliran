<x-layouts.app title="Tambah Instansi (Jemput Bola)">
    <x-page-header title="Tambah Instansi (Jemput Bola)" :back="route('admin.survei-instansi.index')"
        subtitle="Untuk usaha/instansi yang didatangi langsung oleh petugas dan belum punya akun. Instansi langsung terverifikasi atas nama Anda; setelah itu lowongannya bisa diinput di menu Lowongan." />

    <form method="POST" action="{{ route('admin.survei-instansi.store') }}" class="space-y-4">
        @csrf
        @include('instansi.partials.form-instansi', ['instansi' => null, 'namaPj' => null, 'waDefault' => null])

        <x-card title="Hasil Survei Petugas">
            <x-textarea label="Catatan survei" name="catatan_survei" rows="3" required hint="Contoh: dikunjungi 19/11, usaha benar ada, pemilik bersedia menerima pekerja dari program." />
        </x-card>

        <x-button size="lg">Simpan Instansi Terverifikasi</x-button>
    </form>
</x-layouts.app>
