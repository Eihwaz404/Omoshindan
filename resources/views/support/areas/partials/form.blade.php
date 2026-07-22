@php
    $selectedUsers = old('user_ids', $area->users->pluck('id')->all());
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('Nome da área')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $area->name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="slug" :value="__('Slug')" />
            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $area->slug)" required />
            <p class="mt-2 text-xs text-slate-500">Ex.: <code>service_desk</code>, <code>systems</code>.</p>
            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
        </div>

        <div class="lg:col-span-2">
            <x-input-label for="description" :value="__('Descrição')" />
            <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400">{{ old('description', $area->description) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div>
            <label class="inline-flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $area->is_active ?? true)) class="rounded border-slate-700 bg-slate-900 text-cyan-400 focus:ring-cyan-400/30">
                <span class="text-sm text-slate-300">{{ __('Área ativa') }}</span>
            </label>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
        <div class="mb-4">
            <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-300/80">{{ __('Usuários vinculados') }}</h3>
            <p class="mt-2 text-sm text-slate-400">{{ __('Selecione os usuários que podem atuar nessa área.') }}</p>
        </div>

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($users as $user)
                <label class="flex items-start gap-3 rounded-xl border border-slate-800 bg-slate-900/70 p-4 transition hover:border-cyan-400/30">
                    <input
                        type="checkbox"
                        name="user_ids[]"
                        value="{{ $user->id }}"
                        class="mt-1 rounded border-slate-700 bg-slate-900 text-cyan-400 focus:ring-cyan-400/30"
                        @checked(in_array($user->id, $selectedUsers, true))
                    >
                    <span>
                        <span class="block text-sm font-medium text-slate-100">{{ $user->name }}</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-400">{{ $user->email }}</span>
                    </span>
                </label>
            @endforeach
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
        <a href="{{ route('support.areas.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
            <x-heroicon-o-arrow-left class="me-2 h-4 w-4" />
            {{ __('Voltar') }}
        </a>
    </div>
</form>
