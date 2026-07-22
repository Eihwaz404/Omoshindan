<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ $ticket->reference }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ $ticket->subject_label }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                    <x-heroicon-o-rectangle-stack class="me-2 h-3.5 w-3.5" />
                    {{ $ticket->status_label }}
                </span>
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $ticket->priority_badge_classes }}">
                    <x-heroicon-o-bolt class="me-2 h-3.5 w-3.5 {{ $ticket->priority_icon_classes }}" />
                    {{ $ticket->priority_label }}
                </span>
                <span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-200">
                    <x-heroicon-o-building-office-2 class="me-2 h-3.5 w-3.5" />
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

                    <livewire:support.ticket-sla-card :ticket="$ticket" wire:key="ticket-sla-{{ $ticket->id }}" />

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-100">
                            <x-heroicon-o-rectangle-stack class="h-4 w-4 text-cyan-300" />
                            <span>Histórico</span>
                        </h3>

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

                                    @if ($event->attachments->isNotEmpty())
                                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                            @foreach ($event->attachments as $attachment)
                                                <a href="{{ asset('storage/'.$attachment->path) }}" target="_blank" class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/70 transition hover:border-cyan-400/40">
                                                    <img src="{{ asset('storage/'.$attachment->path) }}" alt="{{ $attachment->original_name }}" class="h-40 w-full object-cover">
                                                    <div class="border-t border-slate-800 px-3 py-2">
                                                        <p class="truncate text-xs font-medium text-slate-200">{{ $attachment->original_name }}</p>
                                                        <p class="mt-1 text-xs text-slate-500">{{ number_format($attachment->size / 1024, 1, ',', '.') }} KB</p>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($event->from_status || $event->to_status || $event->from_area_id || $event->to_area_id || $event->from_area || $event->to_area)
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-400">
                                            @if ($event->from_status || $event->to_status)
                                                <span class="rounded-full border border-slate-700 px-3 py-1">
                                                    {{ config('support.statuses.'.$event->from_status, $event->from_status ?? '---') }} → {{ config('support.statuses.'.$event->to_status, $event->to_status ?? '---') }}
                                                </span>
                                            @endif

                                            @if ($event->from_area_id || $event->to_area_id || $event->from_area || $event->to_area)
                                                <span class="rounded-full border border-slate-700 px-3 py-1">
                                                    {{ $event->fromArea?->name ?? $event->from_area ?? '---' }} → {{ $event->toArea?->name ?? $event->to_area ?? '---' }}
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
                            <div class="flex flex-col gap-2">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-slate-100">
                                    <x-heroicon-o-users class="h-4 w-4 text-fuchsia-300" />
                                    <span>Ações da TI</span>
                                </h3>
                                <p class="text-sm text-slate-400">
                                    {{ __('A ordem é simples: assumir coloca o ticket em análise. Depois disso, você pode encaminhar para outra área ou marcar como solucionado. Se o ticket estiver na sua posse, você também pode registrar observações a qualquer momento.') }}
                                </p>
                            </div>

                            @if ($canHandleCurrentArea)
                                <div class="mt-5 space-y-4">
                                    @if ($ticket->assigned_to_id && ! $isAssignedToCurrentUser)
                                        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                                            <p class="text-sm font-semibold text-slate-100">Ticket já assumido</p>
                                            <p class="mt-2 text-sm text-slate-400">
                                                {{ __('Este ticket está na posse de :name. Você ainda pode encaminhá-lo para outra área, mas não pode trabalhar nele enquanto não estiver assumido por você.', ['name' => $ticket->assignedTo?->name ?? 'outro usuário']) }}
                                            </p>
                                        </div>
                                    @endif

                                    @if (in_array($ticket->status, [\App\Models\Ticket::STATUS_OPEN, \App\Models\Ticket::STATUS_PENDING], true) && ! $ticket->assigned_to_id)
                                        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="space-y-1">
                                                    <p class="text-sm font-semibold text-slate-100">1. Assumir ticket</p>
                                                    <p class="text-sm text-slate-400">Você passa a ser o responsável pelo atendimento desta área.</p>
                                                </div>
                                                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-300">Vai para análise</span>
                                            </div>
                                            <form method="POST" action="{{ route('support.tickets.take', $ticket) }}" class="mt-4 space-y-3" enctype="multipart/form-data">
                                                @csrf
                                                <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Observação opcional"></textarea>
                                                @include('support.tickets.partials.attachments')
                                                <x-primary-button type="submit">
                                                    <x-heroicon-o-user-plus class="me-2 h-4 w-4" />
                                                    {{ __('Assumir e mover para análise') }}
                                                </x-primary-button>
                                            </form>
                                        </div>
                                    @elseif ($isAssignedToCurrentUser)
                                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                                            <p class="text-sm font-semibold text-emerald-50">Ticket sob sua posse</p>
                                            <p class="mt-2 text-sm text-emerald-100/80">O ticket está em análise e sob sua responsabilidade. Você pode encaminhar, solucionar ou adicionar observações enquanto conduz o chamado.</p>
                                        </div>
                                    @elseif ($canHandleCurrentArea && ! $isAssignedToCurrentUser)
                                        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                                            <p class="text-sm font-semibold text-slate-100">2. Trabalhar no ticket</p>
                                            <p class="mt-2 text-sm text-slate-400">Você precisa assumir o ticket antes de operar sobre ele.</p>
                                        </div>
                                    @endif

                                    @if ($isAssignedToCurrentUser && $ticket->status === \App\Models\Ticket::STATUS_ANALYSIS)
                                        <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="space-y-1">
                                                    <p class="text-sm font-semibold text-amber-50">Solicitar informações ao usuário</p>
                                                    <p class="text-sm text-amber-100/80">Use quando faltar contexto para continuar o atendimento.</p>
                                                </div>
                                                <span class="rounded-full border border-amber-300/20 px-3 py-1 text-xs font-semibold text-amber-50">Vai para o solicitante</span>
                                            </div>
                                            <form method="POST" action="{{ route('support.tickets.request-info', $ticket) }}" class="mt-4 space-y-3" enctype="multipart/form-data">
                                                @csrf
                                                <textarea name="note" rows="3" class="block w-full rounded-md border-amber-300/30 bg-slate-950/80 text-slate-100 shadow-sm focus:border-amber-300 focus:ring-amber-300" placeholder="Explique quais informações faltam" required></textarea>
                                                @include('support.tickets.partials.attachments')
                                                <x-secondary-button type="submit">
                                                    <x-heroicon-o-magnifying-glass class="me-2 h-4 w-4" />
                                                    {{ __('Devolver ao solicitante') }}
                                                </x-secondary-button>
                                            </form>
                                        </div>
                                    @elseif ($isAssignedToCurrentUser && $ticket->status === \App\Models\Ticket::STATUS_PENDING)
                                        <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4">
                                            <p class="text-sm font-semibold text-amber-50">Aguardando resposta do solicitante</p>
                                            <p class="mt-2 text-sm text-amber-100/80">O ticket já foi devolvido para que o usuário complemente os dados solicitados.</p>
                                        </div>
                                    @endif

                                    @if (in_array($ticket->status, [\App\Models\Ticket::STATUS_ANALYSIS, \App\Models\Ticket::STATUS_PENDING, \App\Models\Ticket::STATUS_OPEN], true))
                                        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="space-y-1">
                                                    <p class="text-sm font-semibold text-slate-100">3. Encaminhar para outra área</p>
                                                    <p class="text-sm text-slate-400">Passe o ticket para outra equipe quando a solução depender dela.</p>
                                                </div>
                                                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-300">Troca de área</span>
                                            </div>
                                            <form method="POST" action="{{ route('support.tickets.transfer', $ticket) }}" class="mt-4 space-y-3" enctype="multipart/form-data">
                                                @csrf
                                                <label class="block">
                                                    <span class="mb-1 block text-sm font-medium text-slate-200">Nova área</span>
                                                    <select name="target_area_id" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" required>
                                                        @foreach ($areas as $area)
                                                            <option value="{{ $area->id }}" @selected($ticket->area_id === $area->id)>{{ $area->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Motivo e contexto do encaminhamento" required></textarea>
                                                @include('support.tickets.partials.attachments')
                                                <x-primary-button type="submit">
                                                    <x-heroicon-o-building-office-2 class="me-2 h-4 w-4" />
                                                    {{ __('Encaminhar') }}
                                                </x-primary-button>
                                            </form>
                                        </div>
                                    @endif

                                    @if (! in_array($ticket->status, [\App\Models\Ticket::STATUS_RESOLVED, \App\Models\Ticket::STATUS_CLOSED], true))
                                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="space-y-1">
                                                    <p class="text-sm font-semibold text-emerald-50">4. Marcar como solucionado</p>
                                                    <p class="text-sm text-emerald-100/80">Use quando o problema estiver resolvido e puder voltar ao solicitante.</p>
                                                </div>
                                                <span class="rounded-full border border-emerald-300/20 px-3 py-1 text-xs font-semibold text-emerald-50">Volta ao usuário</span>
                                            </div>
                                            <form method="POST" action="{{ route('support.tickets.resolve', $ticket) }}" class="mt-4 space-y-3" enctype="multipart/form-data">
                                                @csrf
                                                <textarea name="note" rows="3" class="block w-full rounded-md border-emerald-300/30 bg-slate-950/80 text-slate-100 shadow-sm focus:border-emerald-300 focus:ring-emerald-300" placeholder="Resumo da solução" required></textarea>
                                                @include('support.tickets.partials.attachments')
                                                <x-primary-button type="submit">
                                                    <x-heroicon-o-check-badge class="me-2 h-4 w-4" />
                                                    {{ __('Marcar como solucionado') }}
                                                </x-primary-button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="mt-5 rounded-2xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                                    {{ __('Você é TI, mas não possui vínculo com esta área. As ações de operação ficam ocultas até que a área seja associada ao seu usuário.') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($isRequester && ($ticket->status === \App\Models\Ticket::STATUS_RESOLVED || ($ticket->status === \App\Models\Ticket::STATUS_PENDING && $ticket->assigned_to_id)))
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                            <h3 class="text-base font-semibold text-slate-100">
                                {{ $ticket->status === \App\Models\Ticket::STATUS_RESOLVED ? __('Confirmação do solicitante') : __('Solicitação de informações') }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-400">
                                @if ($ticket->status === \App\Models\Ticket::STATUS_RESOLVED)
                                    {{ __('A TI informou que o problema foi resolvido. Você pode confirmar o fechamento ou devolver para ajuste.') }}
                                @else
                                    {{ __('A TI pediu informações adicionais. Responda e devolva o ticket para :name.', ['name' => $ticket->assignedTo?->name ?? 'o técnico responsável']) }}
                                @endif
                            </p>

                            <div class="mt-4 space-y-3">
                                @if ($ticket->status === \App\Models\Ticket::STATUS_RESOLVED)
                                    <form method="POST" action="{{ route('support.tickets.close', $ticket) }}" class="space-y-3" enctype="multipart/form-data">
                                        @csrf
                                        <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Comentário opcional"></textarea>
                                        @include('support.tickets.partials.attachments')
                                        <x-primary-button type="submit">{{ __('Fechar ticket') }}</x-primary-button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('support.tickets.return', $ticket) }}" class="space-y-3" enctype="multipart/form-data">
                                    @csrf
                                    <textarea name="note" rows="3" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="{{ $ticket->status === \App\Models\Ticket::STATUS_RESOLVED ? __('Explique o que ainda precisa de ajuste') : __('Adicione a informação solicitada') }}" required></textarea>
                                    @include('support.tickets.partials.attachments')
                                    <x-secondary-button type="submit">
                                        {{ $ticket->status === \App\Models\Ticket::STATUS_RESOLVED ? __('Devolver para a TI') : __('Responder e devolver à TI') }}
                                    </x-secondary-button>
                                </form>
                            </div>
                        </div>
                    @endif

                        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-100">
                            <x-heroicon-o-information-circle class="h-4 w-4 text-slate-300" />
                            <span>Adicionar informação</span>
                        </h3>

                        <form method="POST" action="{{ route('support.tickets.comment', $ticket) }}" class="mt-4 space-y-3" enctype="multipart/form-data">
                            @csrf
                            <textarea name="note" rows="4" class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400" placeholder="Detalhes adicionais" required></textarea>
                            @include('support.tickets.partials.attachments')
                            <x-primary-button type="submit">
                                <x-heroicon-o-plus class="me-2 h-4 w-4" />
                                {{ __('Registrar') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
