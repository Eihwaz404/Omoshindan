<div wire:poll.{{ $this->refreshInterval }}s class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">SLA de solução</p>
            <h3 class="mt-2 text-lg font-semibold text-slate-100">Tempo restante para concluir o chamado</h3>
            <p class="mt-1 text-sm text-slate-400">
                O cálculo considera apenas os minutos úteis definidos para a TI e pausa a contagem quando o ticket está pendente.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-950/50 px-4 py-3 text-right">
            <p class="text-sm font-semibold text-slate-100">{{ $sla['display_text'] }}</p>
            <p class="mt-1 text-xs text-slate-500">
                {{ $sla['elapsed_minutes'] }} min consumidos de {{ $sla['priority_minutes'] }} min previstos
            </p>
        </div>
    </div>

    <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-800">
        <div
            class="relative h-full rounded-full transition-all duration-300"
            style="width: {{ $sla['progress_percent'] }}%; background-color: {{ $sla['tone_color'] }}; box-shadow: 0 0 12px {{ $sla['tone_color'] }}66;"
        >
            @if ($sla['progress_percent'] > 0)
                <span class="absolute -right-1 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full border border-white/20" style="background-color: {{ $sla['tone_color'] }}"></span>
            @endif
        </div>
    </div>

    <div class="mt-3 flex items-center justify-between gap-3 text-xs text-slate-400">
        <span>
            @if ($sla['is_paused'])
                Contagem pausada enquanto o ticket aguarda retorno.
            @elseif ($sla['is_complete'])
                Ticket concluído.
            @elseif ($sla['is_overdue'])
                SLA excedido.
            @else
                Prazo em andamento.
            @endif
        </span>
        <span>{{ $sla['remaining_minutes'] }} min restantes</span>
    </div>
</div>
