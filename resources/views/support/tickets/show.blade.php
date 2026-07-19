<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ $ticket->reference }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ $ticket->subject }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                    {{ $ticket->status_label }}
                </span>
                <span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-200">
                    {{ $ticket->area_label }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <div class="grid gap-4 lg:grid-cols-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Solicitante</p>
                                <p class="mt-2 text-sm font-medium text-slate-100">{{ $ticket->requester?->name }}</p>
                                <p class="text-sm text-slate-400">{{ $ticket->requester?->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Responsável atual</p>
                                <p class="mt-2 text-sm font-medium text-slate-100">{{ $ticket->assignedTo?->name ?? 'Não definido' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Criado em</p>
                                <p class="mt-2 text-sm font-medium text-slate-100">{{ $ticket->created_at?->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-slate-800 pt-6">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Descrição do problema</p>
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-200">{{ $ticket->description }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <h3 class="text-base font-semibold text-slate-100">Histórico</h3>

                        <div class="mt-5 space-y-4">
                            @forelse ($ticket->events as $event)
                                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                                    <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-slate-100">{{ $event->type_label }}</p>
                                            <p class="text-xs text-slate-500">{{ $event->actor?->name ?? 'Sistema' }}</p>
                                        </div>
                                        <p class="text-xs text-slate-500">{{ $event->created_at?->format('d/m/Y H:i:s') }}</p>
                                    </div>

                                    @if ($event->note)
                                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-300">{{ $event->note }}</p>
                                    @endif

                                    @if ($event->from_status || $event->to_status || $event->from_area || $event->to_area)
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-400">
                                            @if ($event->from_status || $event->to_status)
                                                <span class="rounded-full border border-slate-700 px-3 py-1">
                                                    {{ config('support.statuses.'.$event->from_status, $event->from_status ?? '---') }} → {{ config('support.statuses.'.$event->to_status, $event->to_status ?? '---') }}
                                                </span>
                                            @endif

                                            @if ($event->from_area || $event->to_area)
                                                <span class="rounded-full border border-slate-700 px-3 py-1">
                                                    {{ config('support.areas.'.$event->from_area.'.label', $event->from_area ?? '---') }} → {{ config('support.areas.'.$event->to_area.'.label', $event->to_area ?? '---') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Nenhum evento registrado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    @if ($isTechnical)
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                            <h3 class="text-base font-semibold text-slate-100">Ações da TI</h3>

                            <div class="mt-4 space-y-3">
                                @if ($canHandleCurrentArea && in_array($ticket->status, [\App\Models\Ticket::STATUS_OPEN, \App\Models\Ticket::STATUS_PENDING], true))
                                    <form method="POST" action="{{ route('support.tickets.take', $ticket) }}" class="space-y-3">
                                        @csrf
                                        <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Observação da assunção"></textarea>
                                        <x-primary-button type="submit">{{ __('Assumir análise') }}</x-primary-button>
                                    </form>
                                @endif

                                @if ($canHandleCurrentArea && in_array($ticket->status, [\App\Models\Ticket::STATUS_ANALYSIS, \App\Models\Ticket::STATUS_OPEN, \App\Models\Ticket::STATUS_PENDING], true))
                                    <form method="POST" action="{{ route('support.tickets.work', $ticket) }}" class="space-y-3">
                                        @csrf
                                        <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="O que foi iniciado"></textarea>
                                        <x-primary-button type="submit">{{ __('Mover para tratativas') }}</x-primary-button>
                                    </form>
                                @endif

                                @if ($canHandleCurrentArea && in_array($ticket->status, [\App\Models\Ticket::STATUS_ANALYSIS, \App\Models\Ticket::STATUS_PROGRESS, \App\Models\Ticket::STATUS_PENDING, \App\Models\Ticket::STATUS_OPEN], true))
                                    <form method="POST" action="{{ route('support.tickets.transfer', $ticket) }}" class="space-y-3">
                                        @csrf
                                        <select name="current_area" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" required>
                                            @foreach ($areas as $key => $area)
                                                <option value="{{ $key }}" @selected($ticket->current_area === $key)>{{ $area['label'] }}</option>
                                            @endforeach
                                        </select>
                                        <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Motivo do encaminhamento" required></textarea>
                                        <x-primary-button type="submit">{{ __('Encaminhar área') }}</x-primary-button>
                                    </form>
                                @endif

                                @if (! in_array($ticket->status, [\App\Models\Ticket::STATUS_RESOLVED, \App\Models\Ticket::STATUS_CLOSED], true))
                                    <form method="POST" action="{{ route('support.tickets.resolve', $ticket) }}" class="space-y-3">
                                        @csrf
                                        <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Resumo da solução" required></textarea>
                                        <x-primary-button type="submit">{{ __('Marcar como solucionado') }}</x-primary-button>
                                    </form>
                                @endif

                                @if (! $canHandleCurrentArea)
                                    <div class="rounded-xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                                        {{ __('Você é TI, mas não possui acesso a esta área. As ações operacionais ficam ocultas até você receber a permissão correspondente.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($isRequester && $ticket->status === \App\Models\Ticket::STATUS_RESOLVED)
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                            <h3 class="text-base font-semibold text-slate-100">Confirmação do solicitante</h3>

                            <div class="mt-4 space-y-3">
                                <form method="POST" action="{{ route('support.tickets.close', $ticket) }}" class="space-y-3">
                                    @csrf
                                    <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Comentário opcional"></textarea>
                                    <x-primary-button type="submit">{{ __('Fechar ticket') }}</x-primary-button>
                                </form>

                                <form method="POST" action="{{ route('support.tickets.return', $ticket) }}" class="space-y-3">
                                    @csrf
                                    <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Explique o que ainda precisa de ajuste" required></textarea>
                                    <x-secondary-button type="submit">{{ __('Devolver para a TI') }}</x-secondary-button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <h3 class="text-base font-semibold text-slate-100">Adicionar informação</h3>

                        <form method="POST" action="{{ route('support.tickets.comment', $ticket) }}" class="mt-4 space-y-3">
                            @csrf
                            <textarea name="note" rows="4" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Detalhes adicionais" required></textarea>
                            <x-primary-button type="submit">{{ __('Registrar') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
