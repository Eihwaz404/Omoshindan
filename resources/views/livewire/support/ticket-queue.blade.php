<div class="space-y-6" wire:poll.{{ $this->refreshInterval }}s>
    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40">
        <div class="grid gap-4 lg:grid-cols-4">
            <div>
                <x-input-label for="queue-search" value="Buscar" />
                <x-text-input
                    id="queue-search"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Assunto ou descrição"
                    wire:model.live.debounce.500ms="search"
                />
            </div>

            <div>
                <x-input-label for="queue-status" value="Status" />
                <select
                    id="queue-status"
                    class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
                    wire:model.live="status"
                >
                    <option value="">Todos</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="queue-area" value="Área" />
                <select
                    id="queue-area"
                    class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
                    wire:model.live="area"
                >
                    <option value="">Todas</option>
                    @foreach ($areas as $supportArea)
                        <option value="{{ $supportArea->id }}">{{ $supportArea->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead class="bg-slate-950/60">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ticket</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Solicitante</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Assunto</th>
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
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-300">
                                <div>{{ $ticket->requester?->name }}</div>
                                <div class="text-slate-500">{{ $ticket->requester?->email }}</div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-300">{{ $ticket->subject_label }}</td>
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
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">
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
