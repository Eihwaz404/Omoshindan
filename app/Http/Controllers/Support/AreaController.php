<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreSupportAreaRequest;
use App\Http\Requests\Support\UpdateSupportAreaRequest;
use App\Models\SupportArea;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('support.areas.manage');

        $areas = SupportArea::query()
            ->withCount(['users', 'tickets'])
            ->with(['users:id,name,email'])
            ->orderBy('name')
            ->paginate(12);

        return view('support.areas.index', [
            'areas' => $areas,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('support.areas.manage');

        return view('support.areas.create', [
            'area' => new SupportArea([
                'is_active' => true,
            ]),
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function store(StoreSupportAreaRequest $request): RedirectResponse
    {
        Gate::authorize('support.areas.manage');

        $data = $request->validated();

        $area = SupportArea::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? str()->slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $area->users()->sync($data['user_ids'] ?? []);

        return redirect()
            ->route('support.areas.edit', $area)
            ->with('status', __('Área criada com sucesso.'));
    }

    public function edit(SupportArea $area): View
    {
        Gate::authorize('support.areas.manage');

        $area->load('users');

        return view('support.areas.edit', [
            'area' => $area,
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function update(UpdateSupportAreaRequest $request, SupportArea $area): RedirectResponse
    {
        Gate::authorize('support.areas.manage');

        $data = $request->validated();

        $area->name = $data['name'];
        $area->slug = $data['slug'] ?? str()->slug($data['name']);
        $area->description = $data['description'] ?? null;
        $area->is_active = (bool) ($data['is_active'] ?? false);
        $area->save();

        $area->users()->sync($data['user_ids'] ?? []);

        return redirect()
            ->route('support.areas.edit', $area)
            ->with('status', __('Área atualizada com sucesso.'));
    }

    public function destroy(SupportArea $area): RedirectResponse
    {
        Gate::authorize('support.areas.manage');

        if ($area->tickets()->exists()) {
            return back()->withErrors([
                'status' => __('Não é possível excluir uma área que já possui tickets vinculados.'),
            ]);
        }

        $area->users()->detach();
        $area->delete();

        return redirect()
            ->route('support.areas.index')
            ->with('status', __('Área excluída com sucesso.'));
    }
}
