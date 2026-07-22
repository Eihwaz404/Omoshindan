<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Configurações') }}</h2>
                <p class="mt-1 text-sm text-slate-400">Parâmetros administrativos do sistema.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf

                <div class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="inline-flex items-center gap-2 text-lg font-semibold text-slate-100">
                            <x-heroicon-o-information-circle class="h-5 w-5 text-cyan-300" />
                            {{ __('Configurações administrativas') }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-400">
                            Grave fila, dashboard, jornada e prioridades em uma única operação.
                        </p>
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            <x-heroicon-o-check-badge class="me-2 h-4 w-4" />
                            {{ __('Salvar configurações') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <div class="mb-6">
                            <h3 class="inline-flex items-center gap-2 text-lg font-semibold text-slate-100">
                                <x-heroicon-c-adjustments-horizontal class="h-5 w-5 text-cyan-300" />
                                {{ __('Fila') }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-400">
                                Ajuste os parâmetros da fila de chamados que serão usados pelo Livewire.
                            </p>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <x-input-label for="queue_refresh_interval_seconds" value="Temp. Att. Fila" />
                                <x-text-input
                                    id="queue_refresh_interval_seconds"
                                    name="queue_refresh_interval_seconds"
                                    type="number"
                                    min="1"
                                    max="300"
                                    value="{{ old('queue_refresh_interval_seconds', $queueRefreshIntervalSeconds) }}"
                                    class="mt-1 block w-full"
                                />
                                <p class="mt-2 text-xs text-slate-500">
                                    Intervalo em segundos para a lista de tickets ser atualizada automaticamente.
                                </p>
                                <x-input-error :messages="$errors->get('queue_refresh_interval_seconds')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="dashboard_refresh_interval_seconds" value="Temp. Att. Dashboard" />
                                <x-text-input
                                    id="dashboard_refresh_interval_seconds"
                                    name="dashboard_refresh_interval_seconds"
                                    type="number"
                                    min="1"
                                    max="300"
                                    value="{{ old('dashboard_refresh_interval_seconds', $dashboardRefreshIntervalSeconds) }}"
                                    class="mt-1 block w-full"
                                />
                                <p class="mt-2 text-xs text-slate-500">
                                    Intervalo em segundos para o painel de indicadores recarregar os dados automaticamente.
                                </p>
                                <x-input-error :messages="$errors->get('dashboard_refresh_interval_seconds')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <div class="mb-6">
                            <h3 class="inline-flex items-center gap-2 text-lg font-semibold text-slate-100">
                                <x-heroicon-o-calendar-days class="h-5 w-5 text-emerald-300" />
                                {{ __('Jornada de trabalho') }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-400">
                                Defina os horários de trabalho do suporte de TI, incluindo o intervalo de almoço.
                            </p>
                        </div>

                        <div class="space-y-4">
                            @foreach ($workingDays as $dayKey => $day)
                                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/50 p-4">
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <h4 class="text-sm font-semibold text-slate-100">{{ $day['label'] }}</h4>
                                        <span class="text-xs uppercase tracking-[0.22em] text-slate-500">Horários</span>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                        <div>
                                            <x-input-label :for="'work_schedule_'.$dayKey.'_start'" value="Entrada" />
                                            <x-text-input
                                                :id="'work_schedule_'.$dayKey.'_start'"
                                                :name="'work_schedule['.$dayKey.'][start]'"
                                                type="time"
                                                value="{{ old('work_schedule.'.$dayKey.'.start', $workSchedule[$dayKey]['start']) }}"
                                                class="mt-1 block w-full"
                                            />
                                            <x-input-error :messages="$errors->get('work_schedule.'.$dayKey.'.start')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label :for="'work_schedule_'.$dayKey.'_end'" value="Saída" />
                                            <x-text-input
                                                :id="'work_schedule_'.$dayKey.'_end'"
                                                :name="'work_schedule['.$dayKey.'][end]'"
                                                type="time"
                                                value="{{ old('work_schedule.'.$dayKey.'.end', $workSchedule[$dayKey]['end']) }}"
                                                class="mt-1 block w-full"
                                            />
                                            <x-input-error :messages="$errors->get('work_schedule.'.$dayKey.'.end')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label :for="'work_schedule_'.$dayKey.'_lunch_start'" value="Almoço início" />
                                            <x-text-input
                                                :id="'work_schedule_'.$dayKey.'_lunch_start'"
                                                :name="'work_schedule['.$dayKey.'][lunch_start]'"
                                                type="time"
                                                value="{{ old('work_schedule.'.$dayKey.'.lunch_start', $workSchedule[$dayKey]['lunch_start']) }}"
                                                class="mt-1 block w-full"
                                            />
                                            <x-input-error :messages="$errors->get('work_schedule.'.$dayKey.'.lunch_start')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label :for="'work_schedule_'.$dayKey.'_lunch_end'" value="Almoço fim" />
                                            <x-text-input
                                                :id="'work_schedule_'.$dayKey.'_lunch_end'"
                                                :name="'work_schedule['.$dayKey.'][lunch_end]'"
                                                type="time"
                                                value="{{ old('work_schedule.'.$dayKey.'.lunch_end', $workSchedule[$dayKey]['lunch_end']) }}"
                                                class="mt-1 block w-full"
                                            />
                                            <x-input-error :messages="$errors->get('work_schedule.'.$dayKey.'.lunch_end')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                        <div class="mb-6">
                            <h3 class="inline-flex items-center gap-2 text-lg font-semibold text-slate-100">
                                <x-heroicon-o-bolt class="h-5 w-5 text-amber-300" />
                                {{ __('Prioridades') }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-400">
                                Defina os níveis de prioridade e o prazo de solução em minutos para cada um deles.
                            </p>
                        </div>

                        <div class="space-y-4">
                            @foreach ($priorityLevels as $priorityKey => $priority)
                                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/50 p-4">
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <h4 class="text-sm font-semibold text-slate-100">{{ $priority['label'] }}</h4>
                                        <span class="text-xs uppercase tracking-[0.22em] text-slate-500">minutos</span>
                                    </div>

                                    <div>
                                        <x-input-label :for="'priority_sla_'.$priorityKey.'_minutes'" value="Tempo para solução" />
                                        <x-text-input
                                            :id="'priority_sla_'.$priorityKey.'_minutes'"
                                            :name="'priority_sla['.$priorityKey.'][minutes]'"
                                            type="number"
                                            min="1"
                                            max="10080"
                                            value="{{ old('priority_sla.'.$priorityKey.'.minutes', $prioritySla[$priorityKey]['minutes']) }}"
                                            class="mt-1 block w-full"
                                        />
                                        <p class="mt-2 text-xs text-slate-500">
                                            Prazo esperado para resolver chamados desta prioridade.
                                        </p>
                                        <x-input-error :messages="$errors->get('priority_sla.'.$priorityKey.'.minutes')" class="mt-2" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
