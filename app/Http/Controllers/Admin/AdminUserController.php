<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Models\Kecamatan;
use App\Models\User;
use App\Services\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $roles = array_map(fn (Role $r) => $r->value, Role::adminRoles());

        $users = User::role($roles)
            ->with(['roles', 'kecamatan', 'permissions'])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('email', 'like', '%'.$request->q.'%')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), ...$this->opsi()]);
    }

    public function store(AdminUserRequest $request, AdminUserService $service): RedirectResponse
    {
        $service->simpan($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Akun admin berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->isAdmin(), 404);
        $user->load(['kecamatan', 'roles', 'permissions']);

        return view('admin.users.form', compact('user') + $this->opsi());
    }

    public function update(AdminUserRequest $request, User $user, AdminUserService $service): RedirectResponse
    {
        abort_unless($user->isAdmin(), 404);

        if ($user->is($request->user()) && ($request->input('role') !== Role::SuperAdmin->value || ! $request->boolean('is_active'))) {
            return back()->with('error', 'Anda tidak bisa menurunkan peran atau menonaktifkan akun sendiri.');
        }

        $service->simpan($request->validated(), $user);

        return redirect()->route('admin.users.index')->with('success', 'Akun admin berhasil diperbarui.');
    }

    /** @return array<string, mixed> */
    private function opsi(): array
    {
        return [
            'roles' => collect(Role::adminRoles())->mapWithKeys(fn (Role $r) => [$r->value => $r->label()])->all(),
            'kecamatan' => Kecamatan::orderBy('nama')->pluck('nama', 'id')->all(),
        ];
    }
}
