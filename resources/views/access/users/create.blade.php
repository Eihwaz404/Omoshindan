<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Novo usuário') }}</h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40 sm:p-8">
                @include('access.users.partials.form', [
                    'action' => route('access.users.store'),
                    'method' => 'POST',
                    'submitLabel' => __('Criar usuário'),
                    'user' => $user,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
