<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimoniRequest;
use App\Models\Testimoni;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Cerita sukses di halaman awal. */
class TestimoniController extends Controller
{
    public function __construct(private FileService $files) {}

    public function index(): View
    {
        return view('admin.testimoni.index', ['testimoni' => Testimoni::orderBy('urutan')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.testimoni.form', ['testimoni' => new Testimoni(['aktif' => true, 'urutan' => 0])]);
    }

    public function store(TestimoniRequest $request): RedirectResponse
    {
        $this->simpan($request, new Testimoni);

        return redirect()->route('admin.testimoni.index')->with('success', 'Cerita sukses ditambahkan.');
    }

    public function edit(Testimoni $testimoni): View
    {
        return view('admin.testimoni.form', compact('testimoni'));
    }

    public function update(TestimoniRequest $request, Testimoni $testimoni): RedirectResponse
    {
        $this->simpan($request, $testimoni);

        return redirect()->route('admin.testimoni.index')->with('success', 'Cerita sukses diperbarui.');
    }

    public function destroy(Testimoni $testimoni): RedirectResponse
    {
        $this->files->hapusPublik($testimoni->foto);
        $testimoni->delete();

        return back()->with('success', 'Cerita sukses dihapus.');
    }

    private function simpan(TestimoniRequest $request, Testimoni $testimoni): void
    {
        $testimoni->fill(collect($request->validated())->except('foto')->all());
        if ($request->hasFile('foto')) {
            $testimoni->foto = $this->files->simpanPublik($request->file('foto'), 'testimoni', $testimoni->foto);
        }
        $testimoni->save();
    }
}
