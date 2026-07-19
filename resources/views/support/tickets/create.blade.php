<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Novo ticket') }}</h2>
            <p class="mt-1 text-sm text-slate-400">{{ __('O sistema vai classificar e direcionar o chamado automaticamente para a área mais provável.') }}</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40 sm:p-8">
                <form method="POST" action="{{ route('support.tickets.store') }}" class="space-y-6">
                    @csrf

                    @include('support.tickets.partials.form', ['ticket' => $ticket])

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('support.tickets.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>{{ __('Abrir ticket') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
