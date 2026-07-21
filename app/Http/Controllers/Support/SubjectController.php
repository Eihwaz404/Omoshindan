<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreSupportSubjectRequest;
use App\Http\Requests\Support\UpdateSupportSubjectRequest;
use App\Models\SupportSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        Gate::authorize('support.areas.manage');

        $subjects = SupportSubject::query()
            ->withCount(['tickets'])
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(15);

        return view('support.subjects.index', [
            'subjects' => $subjects,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('support.areas.manage');

        return view('support.subjects.create', [
            'subject' => new SupportSubject([
                'is_active' => true,
                'category' => 1,
            ]),
        ]);
    }

    public function store(StoreSupportSubjectRequest $request): RedirectResponse
    {
        Gate::authorize('support.areas.manage');

        $data = $request->validated();

        $subject = SupportSubject::create([
            'category' => (int) $data['category'],
            'name' => $data['name'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()
            ->route('support.subjects.edit', $subject)
            ->with('status', __('Assunto criado com sucesso.'));
    }

    public function edit(SupportSubject $subject): View
    {
        Gate::authorize('support.areas.manage');

        return view('support.subjects.edit', [
            'subject' => $subject,
        ]);
    }

    public function update(UpdateSupportSubjectRequest $request, SupportSubject $subject): RedirectResponse
    {
        Gate::authorize('support.areas.manage');

        $data = $request->validated();

        $subject->category = (int) $data['category'];
        $subject->name = $data['name'];
        $subject->is_active = (bool) ($data['is_active'] ?? false);
        $subject->save();

        return redirect()
            ->route('support.subjects.edit', $subject)
            ->with('status', __('Assunto atualizado com sucesso.'));
    }

    public function destroy(SupportSubject $subject): RedirectResponse
    {
        Gate::authorize('support.areas.manage');

        if ($subject->tickets()->exists()) {
            return back()->withErrors([
                'status' => __('Não é possível excluir um assunto que já possui tickets vinculados.'),
            ]);
        }

        $subject->delete();

        return redirect()
            ->route('support.subjects.index')
            ->with('status', __('Assunto excluído com sucesso.'));
    }
}
