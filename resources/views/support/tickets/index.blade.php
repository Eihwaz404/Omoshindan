<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Tickets') }}</h2>
                <p class="mt-1 text-sm text-slate-400">Abertura, encaminhamento e acompanhamento do suporte.</p>
            </div>

            <a href="{{ route('support.tickets.create') }}" class="inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/15 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-500/25 hover:text-white">
                {{ __('Novo ticket') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <form method="GET" class="mb-6 rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40">
                <div class="grid gap-4 lg:grid-cols-4">
                    <div>
                        <x-input-label for="search" value="Buscar" />
                        <x-text-input id="search" name="search" value="{{ $filters['search'] }}" class="mt-1 block w-full" placeholder="Assunto ou descrição" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400">
                            <option value="">Todos</option>
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="area" value="Área" />
                        <select id="area" name="area" class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400">
                            <option value="">Todas</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected($filters['area'] === (string) $area->id)>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <x-primary-button type="submit">{{ __('Filtrar') }}</x-primary-button>
                        <a href="{{ route('support.tickets.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
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
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ticket</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Solicitante</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Área</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse ($tickets as $ticket)
                                <tr class="hover:bg-slate-950/30">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-medium text-slate-100">{{ $ticket->reference }}</div>
                                        <div class="text-sm text-slate-400">{{ $ticket->subject }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-300">
                                        <div>{{ $ticket->requester?->name }}</div>
                                        <div class="text-slate-500">{{ $ticket->requester?->email }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-300">{{ $ticket->area_label }}</td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                                            {{ $ticket->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="{{ route('support.tickets.show', $ticket) }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                                            {{ __('Abrir') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400">
                                        Nenhum ticket encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-800 px-6 py-4">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
