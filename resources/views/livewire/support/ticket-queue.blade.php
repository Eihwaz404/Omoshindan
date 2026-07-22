<div class="space-y-6" wire:poll.{{ $this->refreshInterval }}s>
    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40">
        <div class="grid gap-4 lg:grid-cols-4">
            <div>
                <label for="queue-search" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
                    <x-heroicon-o-magnifying-glass class="h-4 w-4 text-slate-300" />
                    <span>Buscar</span>
                </label>
                <x-text-input
                    id="queue-search"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Assunto ou descrição"
                    wire:model.live.debounce.500ms="search"
                />
            </div>

            <div>
                <label for="queue-status" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
                    <x-heroicon-o-rectangle-stack class="h-4 w-4 text-cyan-300" />
                    <span>Status</span>
                </label>
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
                <label for="queue-area" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
                    <x-heroicon-o-building-office-2 class="h-4 w-4 text-emerald-300" />
                    <span>Área</span>
                </label>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-clipboard-document-list class="h-4 w-4 text-cyan-300" />
                                <span>Ticket</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-users class="h-4 w-4 text-fuchsia-300" />
                                <span>Solicitante</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-document-text class="h-4 w-4 text-slate-300" />
                                <span>Assunto</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-bolt class="h-4 w-4 text-amber-300" />
                                <span>Prioridade</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-calendar-days class="h-4 w-4 text-emerald-300" />
                                <span>SLA</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-building-office-2 class="h-4 w-4 text-emerald-300" />
                                <span>Área</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-rectangle-stack class="h-4 w-4 text-cyan-300" />
                                <span>Status</span>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-slate-950/30">
                            <td class="px-6 py-5">
                                <div class="text-sm font-medium text-slate-100">{{ $ticket->reference }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $ticket->created_at?->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-300">
                                <div>{{ $ticket->requester?->name }}</div>
                                <div class="text-slate-500">{{ $ticket->requester?->email }}</div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-300">{{ $ticket->subject_label }}</td>
                            <td class="px-6 py-5 text-sm text-slate-300">{{ $ticket->priority_label }}</td>
                            @php($sla = $ticket->getAttribute('sla') ?? $ticket->slaSummary())
                            <td class="px-6 py-5">
                                <div class="space-y-2">
                                    <div class="text-sm font-medium text-slate-100">{{ $sla['display_text'] }}</div>
                                    <div class="h-2 overflow-hidden rounded-full bg-slate-800">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $sla['tone'] }}" style="width: {{ $sla['progress_percent'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $sla['elapsed_minutes'] }} / {{ $sla['priority_minutes'] }} min
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-300">{{ $ticket->area_label }}</td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('support.tickets.show', $ticket) }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                                    <x-heroicon-o-eye class="me-2 h-4 w-4" />
                                    {{ __('Abrir') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-400">
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
