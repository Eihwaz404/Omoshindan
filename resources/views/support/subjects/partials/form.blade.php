<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <x-input-label for="category" :value="__('Categoria')" />
            <x-text-input id="category" name="category" type="number" min="1" class="mt-1 block w-full" :value="old('category', $subject->category)" required autofocus />
            <p class="mt-2 text-xs text-slate-500">Use um número para agrupar assuntos parecidos, como 1, 2, 3.</p>
            <x-input-error class="mt-2" :messages="$errors->get('category')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Assunto')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $subject->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="inline-flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $subject->is_active ?? true)) class="rounded border-slate-700 bg-slate-900 text-cyan-400 focus:ring-cyan-400/30">
                <span class="text-sm text-slate-300">{{ __('Assunto ativo') }}</span>
            </label>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            {{ __('Revise os campos destacados antes de continuar.') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <x-primary-button>
            <x-heroicon-o-check-badge class="me-2 h-4 w-4" />
            {{ $submitLabel }}
        </x-primary-button>
        <a href="{{ route('support.subjects.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
            <x-heroicon-o-arrow-left class="me-2 h-4 w-4" />
            {{ __('Voltar') }}
        </a>
    </div>
</form>
