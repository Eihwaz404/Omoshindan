<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">
                    {{ __('Usuários') }}
                </h2>
                <p class="mt-1 text-sm text-slate-400">Gestão de acesso, status e permissões.</p>
            </div>

            @can('users.create')
                <a href="{{ route('access.users.create') }}" class="inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/15 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-500/25 hover:text-white">
                    <x-heroicon-o-user-plus class="me-2 h-4 w-4" />
                    {{ __('Novo usuário') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('status'))
                <div class="mb-6 rounded-lg border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    {{ $errors->first('status') }}
                </div>
            @endif

            <form method="GET" class="mb-6">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="flex-1">
                        <x-text-input name="search" value="{{ $search }}" class="block w-full" placeholder="Buscar por nome ou e-mail" />
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button type="submit">
                            <x-heroicon-o-magnifying-glass class="me-2 h-4 w-4" />
                            {{ __('Buscar') }}
                        </x-primary-button>
                        <a href="{{ route('access.users.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                            <x-heroicon-o-arrow-path class="me-2 h-4 w-4" />
                            {{ __('Limpar') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800">
                        <thead class="bg-slate-950/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Usuário</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Perfil</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Permissões</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse ($users as $user)
                                <tr class="hover:bg-slate-950/30">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-medium text-slate-100">{{ $user->name }}</div>
                                        <div class="text-sm text-slate-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-300">
                                        {{ $roles[$user->role] ?? $user->role }}
                                    </td>
                                    <td class="px-6 py-5">
                                        @if ($user->is_active)
                                            <span class="inline-flex items-center rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">Ativo</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full border border-rose-400/20 bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-200">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-300">
                                        @if (in_array($user->role, ['super_admin', 'admin'], true))
                                            Tudo
                                        @else
                                            {{ collect($user->permissions ?? [])->count() }} permissões
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex justify-end gap-2">
                                            @can('users.update')
                                                <a href="{{ route('access.users.edit', $user) }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                                                    <x-heroicon-o-pencil-square class="me-2 h-4 w-4" />
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('users.toggle')
                                                <form method="POST" action="{{ route('access.users.toggle', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/10 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-cyan-100 transition hover:bg-cyan-500/20">
                                                        <x-heroicon-o-arrow-path class="me-2 h-4 w-4" />
                                                        {{ $user->is_active ? 'Desativar' : 'Ativar' }}
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('users.delete')
                                                @if (! auth()->user()->is($user))
                                                    <button
                                                        type="button"
                                                        x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'delete-user-{{ $user->id }}')"
                                                        class="inline-flex items-center rounded-md border border-rose-400/30 bg-rose-500/10 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-rose-100 transition hover:bg-rose-500/20"
                                                    >
                                                        <x-heroicon-o-trash class="me-2 h-4 w-4" />
                                                        Excluir
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">
                                        Nenhum usuário encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-800 px-6 py-4">
                    {{ $users->links() }}
                </div>

                @foreach ($users as $user)
                    @if (! auth()->user()->is($user))
                        <x-modal name="delete-user-{{ $user->id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('access.users.destroy', $user) }}" class="p-6">
                                @csrf
                                @method('DELETE')

                                <h2 class="text-lg font-medium text-slate-100">
                                    {{ __('Excluir usuário') }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-400">
                                    {{ __('Essa ação vai remover o usuário do sistema. Esta operação não pode ser desfeita.') }}
                                </p>

                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        <x-heroicon-o-arrow-left class="me-2 h-4 w-4" />
                                        {{ __('Cancelar') }}
                                    </x-secondary-button>

                                    <x-danger-button class="ms-3">
                                        <x-heroicon-o-trash class="me-2 h-4 w-4" />
                                        {{ __('Excluir') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
