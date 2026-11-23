@props(['penyedia' => false])
<p {{ $attributes->merge(['class' => 'rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900']) }}>
    <strong>Usia minimal 18 tahun.</strong>
    @if ($penyedia)
        Penanggung jawab penyedia kerja harus berusia 18 tahun ke atas.
    @else
        Pendaftaran pencari kerja dan pencari pelatihan hanya untuk peserta berusia 18 tahun ke atas.
    @endif
    Pastikan Anda sudah memiliki KTP dan isi nama serta tanggal lahir sesuai KTP.
</p>
