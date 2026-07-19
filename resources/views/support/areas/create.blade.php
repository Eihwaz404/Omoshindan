<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Nova área') }}</h2>
            <p class="mt-1 text-sm text-slate-400">{{ __('Cadastre a área e selecione os usuários que podem atuar nela.') }}</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40 sm:p-8">
                @include('support.areas.partials.form', [
                    'action' => route('support.areas.store'),
                    'method' => 'POST',
                    'submitLabel' => __('Criar área'),
                    'area' => $area,
                    'users' => $users,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
