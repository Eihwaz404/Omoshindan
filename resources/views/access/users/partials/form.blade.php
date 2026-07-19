@php
    $permissionGroups = $permissionGroups ?? config('permissions.groups', []);
    $roles = $roles ?? config('permissions.roles', []);
    $selectedPermissions = old('permissions', $user->permissions ?? []);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Perfil')" />
            <select id="role" name="role" class="mt-1 block w-full rounded-md border border-slate-700 bg-slate-900/90 text-slate-100 shadow-sm shadow-slate-950/30 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/25">
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('role')" />
        </div>

        <div>
            <label class="mt-6 inline-flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded border-slate-700 bg-slate-900 text-cyan-400 focus:ring-cyan-400/30">
                <span class="text-sm text-slate-300">{{ __('Usuário ativo') }}</span>
            </label>
        </div>

        <div class="lg:col-span-2">
            <x-input-label for="password" :value="$method === 'POST' ? __('Senha') : __('Nova senha')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div class="lg:col-span-2">
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        </div>
    </div>

    @if ($canManagePermissions ?? false)
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
            <div class="mb-4">
                <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-300/80">{{ __('Permissões') }}</h3>
                <p class="mt-2 text-sm text-slate-400">{{ __('Selecione apenas o que esse usuário realmente precisa acessar.') }}</p>
            </div>

            <div class="space-y-6">
                @foreach ($permissionGroups as $group)
                    <div>
                        <h4 class="mb-3 text-sm font-medium text-slate-200">{{ $group['label'] ?? 'Grupo' }}</h4>
                        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach (($group['permissions'] ?? []) as $permission => $meta)
                                <label class="flex items-start gap-3 rounded-xl border border-slate-800 bg-slate-900/70 p-4 transition hover:border-cyan-400/30">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission }}"
                                        class="mt-1 rounded border-slate-700 bg-slate-900 text-cyan-400 focus:ring-cyan-400/30"
                                        @checked(in_array($permission, $selectedPermissions, true))
                                    >
                                    <span>
                                        <span class="block text-sm font-medium text-slate-100">{{ $meta['label'] ?? $permission }}</span>
                                        <span class="mt-1 block text-xs leading-5 text-slate-400">{{ $meta['description'] ?? '' }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 text-sm text-slate-400">
            {{ __('Permissões detalhadas ficam disponíveis apenas para perfis com gestão de permissões.') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            {{ __('Revise os campos destacados antes de continuar.') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('access.users.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
            {{ __('Voltar') }}
        </a>
    </div>
</form>
