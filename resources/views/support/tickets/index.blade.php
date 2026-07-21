<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Tickets') }}</h2>
                <p class="mt-1 text-sm text-slate-400">Abertura, encaminhamento e acompanhamento do suporte.</p>
            </div>

            <a href="{{ route('support.tickets.create') }}" class="inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/15 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-500/25 hover:text-white">
                {{ __('Novo ticket') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            <livewire:support.ticket-queue />
        </div>
    </div>
</x-app-layout>
