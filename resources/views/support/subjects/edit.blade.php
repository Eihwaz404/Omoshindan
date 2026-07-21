<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Editar assunto') }}</h2>
            <p class="mt-1 text-sm text-slate-400">{{ __('Categoria :category · :name', ['category' => $subject->category, 'name' => $subject->name]) }}</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40 sm:p-8">
                @include('support.subjects.partials.form', [
                    'action' => route('support.subjects.update', $subject),
                    'method' => 'PUT',
                    'submitLabel' => __('Salvar alterações'),
                    'subject' => $subject,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
