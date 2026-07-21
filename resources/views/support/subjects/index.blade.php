<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Assuntos') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('Cadastro de assuntos usados na abertura dos tickets, agrupados por categoria.') }}</p>
            </div>

            <a href="{{ route('support.subjects.create') }}" class="inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/15 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-500/25 hover:text-white">
                {{ __('Novo assunto') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800">
                        <thead class="bg-slate-950/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Categoria</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Assunto</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Tickets</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse ($subjects as $subject)
                                <tr class="hover:bg-slate-950/30">
                                    <td class="px-6 py-5 text-sm text-slate-300">{{ $subject->category }}</td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-medium text-slate-100">{{ $subject->name }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-300">{{ $subject->tickets_count }}</td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $subject->is_active ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-100' : 'border-slate-700 bg-slate-800 text-slate-300' }}">
                                            {{ $subject->is_active ? __('Ativo') : __('Inativo') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('support.subjects.edit', $subject) }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                                                {{ __('Editar') }}
                                            </a>

                                            <form method="POST" action="{{ route('support.subjects.destroy', $subject) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-secondary-button type="submit">{{ __('Excluir') }}</x-secondary-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">
                                        Nenhum assunto cadastrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-800 px-6 py-4">
                    {{ $subjects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
