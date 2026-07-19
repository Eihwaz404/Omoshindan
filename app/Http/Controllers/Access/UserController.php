<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\StoreUserRequest;
use App\Http\Requests\Access\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('users.view');

        $search = trim((string) $request->string('search'));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('access.users.index', [
            'users' => $users,
            'search' => $search,
            'roles' => config('permissions.roles'),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('users.create');

        return view('access.users.create', [
            'user' => new User([
                'role' => 'user',
                'is_active' => true,
                'permissions' => [],
            ]),
            'roles' => config('permissions.roles'),
            'permissionGroups' => config('permissions.groups'),
            'canManagePermissions' => $request->user()->hasPermission('users.permissions'),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('users.create');

        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'permissions' => $request->user()->hasPermission('users.permissions')
                ? array_values($data['permissions'] ?? [])
                : [],
        ]);

        return redirect()
            ->route('access.users.index')
            ->with('status', __('Usuário criado com sucesso.'));
    }

    public function edit(Request $request, User $user): View
    {
        Gate::authorize('users.update');

        return view('access.users.edit', [
            'user' => $user,
            'roles' => config('permissions.roles'),
            'permissionGroups' => config('permissions.groups'),
            'canManagePermissions' => $request->user()->hasPermission('users.permissions'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('users.update');

        $data = $request->validated();
        $canManagePermissions = $request->user()->hasPermission('users.permissions');

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->is_active = (bool) ($data['is_active'] ?? false);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($canManagePermissions) {
            $user->permissions = array_values($data['permissions'] ?? []);
        }

        $user->save();

        return redirect()
            ->route('access.users.edit', $user)
            ->with('status', __('Usuário atualizado com sucesso.'));
    }

    public function toggle(User $user): RedirectResponse
    {
        Gate::authorize('users.toggle');

        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'status' => __('Você não pode desativar sua própria conta aqui.'),
            ]);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('status', $user->is_active
            ? __('Usuário ativado com sucesso.')
            : __('Usuário desativado com sucesso.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('users.delete');

        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'status' => __('Você não pode excluir a sua própria conta por aqui.'),
            ]);
        }

        $user->delete();

        return redirect()
            ->route('access.users.index')
            ->with('status', __('Usuário excluído com sucesso.'));
    }
}
