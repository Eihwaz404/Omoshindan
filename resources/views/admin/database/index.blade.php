<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-100">{{ __('Banco de dados') }}</h2>
                <p class="mt-1 text-sm text-slate-400">{{ __('Ferramentas administrativas para manutenção controlada de tabelas do sistema.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-lg shadow-slate-950/40">
                    <h3 class="text-base font-semibold text-slate-100">{{ __('Tabelas controladas') }}</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ __('Selecione a tabela que deseja sanitizar. A operação remove todos os registros e reinicia os índices da tabela e de seus históricos relacionados.') }}
                    </p>

                    <div class="mt-6 overflow-hidden rounded-xl border border-slate-800">
                        <table class="min-w-full divide-y divide-slate-800">
                            <thead class="bg-slate-950/60">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Tabela</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Descrição</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Registros</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-950/30">
                                @foreach ($tables as $table)
                                    <tr>
                                        <td class="px-4 py-4 text-sm font-medium text-slate-100">{{ $table['label'] }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-400">{{ $table['description'] }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-300">{{ number_format($table['count'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-6 shadow-lg shadow-slate-950/40">
                    <h3 class="text-base font-semibold text-rose-50">{{ __('Sanitizar tabela') }}</h3>
                    <p class="mt-2 text-sm text-rose-100/80">
                        {{ __('Esta ação é destrutiva. Ela apaga os registros da tabela selecionada e também o histórico relacionado aos tickets.') }}
                    </p>

                    <form method="POST" action="{{ route('admin.database.sanitize') }}" class="mt-6 space-y-4">
                        @csrf

                        <label class="block">
                            <span class="mb-1 block text-sm font-medium text-rose-100">Tabela</span>
                            <select name="table" class="block w-full rounded-md border-rose-300/30 bg-slate-950/80 text-slate-100 shadow-sm focus:border-rose-300 focus:ring-rose-300" required>
                                @foreach ($tables as $table)
                                    <option value="{{ $table['key'] }}">{{ $table['label'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="rounded-xl border border-rose-300/20 bg-slate-950/40 p-4 text-sm text-rose-100/90">
                            {{ __('Confirme apenas se tiver certeza absoluta de que deseja zerar os tickets e seus eventos.') }}
                        </div>

                        <x-danger-button type="submit" onclick="return confirm('Isso apagará todos os tickets e eventos e reiniciará os IDs. Continuar?')">
                            {{ __('Sanitizar tabela') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
