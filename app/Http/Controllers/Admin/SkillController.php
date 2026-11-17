<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterNamaRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function index(): View
    {
        return view('admin.master.nama', [
            'judul' => 'Skill',
            'keterangan' => 'Daftar kemampuan yang bisa dipilih pendaftar dan diminta lowongan.',
            'items' => Skill::orderBy('nama')->paginate(50),
            'route' => 'admin.skill',
        ]);
    }

    public function store(MasterNamaRequest $request): RedirectResponse
    {
        Skill::create($request->validated());

        return back()->with('success', 'Skill ditambahkan.');
    }

    public function update(MasterNamaRequest $request, Skill $skill): RedirectResponse
    {
        $skill->update($request->validated());

        return back()->with('success', 'Skill diperbarui.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return back()->with('success', 'Skill dihapus.');
    }
}
