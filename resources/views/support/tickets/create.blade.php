<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start gap-3">
            <div class="rounded-xl border border-cyan-400/20 bg-cyan-500/10 p-2 text-cyan-200">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Novo ticket') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('Escolha o assunto, a prioridade e a área de suporte responsável ao abrir o chamado.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40 sm:p-8">
                <form method="POST" action="{{ route('support.tickets.store') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    @include('support.tickets.partials.form', ['ticket' => $ticket])

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('support.tickets.index') }}" class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition hover:bg-slate-700">
                            <x-heroicon-o-arrow-left class="me-2 h-4 w-4" />
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>
                            <x-heroicon-o-folder-plus class="me-2 h-4 w-4" />
                            {{ __('Abrir ticket') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
