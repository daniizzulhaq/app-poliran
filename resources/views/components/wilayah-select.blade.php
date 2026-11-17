@props(['kecamatan' => [], 'kecamatanId' => null, 'desaId' => null])
@php
    $kecId = old('kecamatan_id', $kecamatanId);
    $dId = old('desa_id', $desaId);
@endphp
<div x-data="wilayahSelect(@js((string) $kecId), @js((string) $dId), @js(route('wilayah.desa', '__ID__')))" class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="form-label">Kabupaten</label>
        <input type="text" value="Timor Tengah Selatan" class="form-control" disabled>
    </div>
    <div>
        <label for="f_kecamatan_id" class="form-label">Kecamatan <span class="text-red-600">*</span></label>
        <select id="f_kecamatan_id" name="kecamatan_id" x-model="kecamatanId" required class="form-control">
            <option value="">- Pilih kecamatan -</option>
            @foreach ($kecamatan as $id => $nama)
                <option value="{{ $id }}" @selected((string) $kecId === (string) $id)>{{ $nama }}</option>
            @endforeach
        </select>
        @error('kecamatan_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="f_desa_id" class="form-label">Desa / Kelurahan <span class="text-red-600">*</span></label>
        <select id="f_desa_id" name="desa_id" x-model="desaId" required class="form-control" :disabled="!kecamatanId || memuat">
            <option value="" x-text="memuat ? 'Memuat...' : '- Pilih desa -'"></option>
            <template x-for="d in desa" :key="d.id">
                <option :value="String(d.id)" x-text="d.nama" :selected="String(d.id) === desaId"></option>
            </template>
        </select>
        @error('desa_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
