{{-- Edukasi pencegahan calo kerja & tindak pidana perdagangan orang (TPPO). --}}
<div class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="text-sm font-semibold tracking-wide text-red-700 uppercase">Waspada</p>
    <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Hati-hati Calo Kerja &amp; Perdagangan Orang</h2>
    <p class="mt-3 max-w-3xl text-slate-700">
        Banyak warga ditawari kerja di luar daerah atau luar negeri dengan janji gaji besar, lalu berangkat tanpa dokumen resmi
        dan akhirnya dieksploitasi. Kenali tandanya sebelum menerima tawaran kerja.
    </p>

    <ul class="mt-6 grid gap-4 sm:grid-cols-2">
        @foreach ([
            ['Program POLIRAN gratis', 'Petugas Polres tidak pernah meminta uang dalam bentuk apa pun, baik untuk pendaftaran, pelatihan, maupun penyaluran kerja.'],
            ['Jangan serahkan dokumen asli', 'Tolak bila perekrut meminta KTP, ijazah, atau akta kelahiran asli untuk "disimpan". Cukup berikan fotokopi.'],
            ['Kerja luar negeri harus resmi', 'Berangkat hanya lewat perusahaan penempatan (P3MI) berizin, dengan perjanjian kerja dan visa kerja. Hindari jalur "cepat" atau visa turis.'],
            ['Waspadai biaya dan utang', 'Curigai tawaran yang meminta biaya besar di depan atau memotong gaji untuk "mengganti ongkos" berbulan-bulan.'],
            ['Minta kontrak tertulis', 'Pastikan nama perusahaan, alamat tempat kerja, gaji, dan jam kerja tertulis jelas sebelum berangkat.'],
            ['Kabari keluarga', 'Beri tahu keluarga alamat tempat kerja dan nomor yang bisa dihubungi. Jangan berangkat diam-diam.'],
        ] as [$judul, $isi])
            <li class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="font-semibold text-slate-900">{{ $judul }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $isi }}</p>
            </li>
        @endforeach
    </ul>

    <p class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-900">
        <strong>Curiga ada calo atau perekrutan ilegal?</strong> Laporkan ke Polres Timor Tengah Selatan atau hubungi Call Center Polri <strong>110</strong> (gratis).
    </p>
</div>
