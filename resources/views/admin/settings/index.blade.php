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

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-slate-100">{{ __('Fila') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">
                            Ajuste os parâmetros da fila de chamados que serão usados pelo Livewire.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
                        @csrf

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

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Salvar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
