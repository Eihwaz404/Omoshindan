<div class="space-y-8" wire:poll.{{ $this->refreshInterval }}s>
    <section class="relative overflow-hidden rounded-3xl border border-cyan-400/15 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 shadow-2xl shadow-slate-950/50">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(34,211,238,0.18),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(14,165,233,0.12),transparent_28%)]"></div>
        <div class="relative grid gap-8 p-6 lg:grid-cols-[1.5fr_1fr] lg:p-8">
            <div class="space-y-4">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-300/80">Operação ativa</p>
                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold text-slate-50 sm:text-4xl">
                        {{ __('Indicadores em tempo real do suporte.') }}
                    </h1>
                    <p class="max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                        {{ __('Este painel resume o comportamento da fila de chamados, a distribuição por status e a carga de trabalho entre as áreas e os técnicos.') }}
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/60 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total de chamados</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-50">{{ $totalTickets }}</div>
                    <div class="mt-2 text-sm text-slate-400">{{ __('Todos os tickets cadastrados no sistema.') }}</div>
                </div>

                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/60 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Áreas monitoradas</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-50">{{ $areaCards->count() }}</div>
                    <div class="mt-2 text-sm text-slate-400">{{ __('Áreas ativas com chamados vinculados.') }}</div>
                </div>

                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/60 p-4">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Usuários da TI</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-50">{{ $technicalUsers->count() }}</div>
                    <div class="mt-2 text-sm text-slate-400">{{ __('Usuários com perfil técnico ou administrativo.') }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-100">Chamados por status</h3>
            <p class="mt-1 text-sm text-slate-400">Distribuição da fila de suporte em cada etapa do fluxo.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @forelse ($statusCards as $status)
                <article class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-lg shadow-slate-950/40">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300/80">{{ $status['label'] }}</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-50">{{ $status['count'] }}</p>
                        </div>
                        <span class="rounded-full border border-cyan-400/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                            {{ $status['status'] }}
                        </span>
                    </div>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-800">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-sky-500"
                            style="width: {{ $statusMax > 0 ? (int) round(($status['count'] / $statusMax) * 100) : 0 }}%"
                        ></div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 text-sm text-slate-400 xl:col-span-5">
                    Nenhum status disponível.
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
            <div class="border-b border-slate-800 px-6 py-5">
                <h3 class="text-lg font-semibold text-slate-100">Chamados por área de suporte</h3>
                <p class="mt-1 text-sm text-slate-400">Volume atual de tickets distribuídos entre as áreas.</p>
            </div>

            <div class="space-y-4 p-6">
                @forelse ($areaCards as $area)
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-950/50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-100">{{ $area->name }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ $area->description }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-semibold text-slate-50">{{ $area->tickets_count }}</p>
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500">tickets</p>
                            </div>
                        </div>

                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-800">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400"
                                style="width: {{ $areaMax > 0 ? (int) round(($area->tickets_count / $areaMax) * 100) : 0 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-950/50 p-6 text-sm text-slate-400">
                        Nenhuma área encontrada.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-lg shadow-slate-950/40">
            <div class="border-b border-slate-800 px-6 py-5">
                <h3 class="text-lg font-semibold text-slate-100">Chamados por usuário do TI</h3>
                <p class="mt-1 text-sm text-slate-400">Quantidade de chamados atualmente atribuídos a cada técnico.</p>
            </div>

            <div class="space-y-4 p-6">
                @forelse ($technicalUsers as $user)
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-950/50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-100">{{ $user->name }}</p>
                                <p class="mt-1 text-sm text-slate-400">{{ $user->email }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-semibold text-slate-50">{{ $user->assigned_tickets_count }}</p>
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500">atribuídos</p>
                            </div>
                        </div>

                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-800">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-fuchsia-400 to-cyan-400"
                                style="width: {{ $technicalMax > 0 ? (int) round(($user->assigned_tickets_count / $technicalMax) * 100) : 0 }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-800/80 bg-slate-950/50 p-6 text-sm text-slate-400">
                        Nenhum usuário técnico encontrado.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
