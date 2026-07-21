<div x-data="{ fields: 1, maxFields: 8 }" class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300/80">{{ __('Imagens') }}</h4>
            <p class="mt-2 text-sm text-slate-400">
                {{ __('Apenas .jpg, até :size MB cada, no máximo :count imagens por envio.', ['size' => '1,5', 'count' => 8]) }}
            </p>
        </div>

        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-cyan-400/30 bg-cyan-500/15 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-cyan-100 transition hover:bg-cyan-500/25 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
            @click.prevent="if (fields < maxFields) fields++"
            :disabled="fields >= maxFields"
        >
            <x-heroicon-o-plus class="h-4 w-4" />
            <span>{{ __('Adicionar imagem') }}</span>
        </button>
    </div>

    <div class="mt-4 space-y-3">
        <template x-for="index in fields" :key="index">
            <label class="block rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <span class="mb-2 block text-sm font-medium text-slate-200">
                    {{ __('Imagem') }} <span x-text="index"></span>
                </span>
                <input
                    type="file"
                    name="images[]"
                    accept=".jpg,.jpeg,image/jpeg"
                    class="block w-full rounded-md border-slate-700 bg-slate-950/80 text-sm text-slate-200 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
                >
            </label>
        </template>
    </div>

    <p class="mt-3 text-xs leading-5 text-slate-500">
        {{ __('Você pode enviar mais imagens depois, em novas ações do ticket.') }}
    </p>
</div>
