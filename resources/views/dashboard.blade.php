<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Painel operacional</p>
            <h2 class="text-xl font-semibold leading-tight text-slate-100">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-sm text-slate-400">
                {{ __('Acompanhe o volume de chamados por status, por área de suporte e por usuário da TI.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <livewire:dashboard-overview />
        </div>
    </div>
</x-app-layout>
