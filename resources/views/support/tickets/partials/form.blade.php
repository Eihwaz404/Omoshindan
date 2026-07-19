<div class="grid gap-6">
    <div>
        <x-input-label for="subject" value="Assunto" />
        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" :value="old('subject', $ticket->subject ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('subject')" />
    </div>

    <div class="rounded-xl border border-cyan-400/20 bg-cyan-500/10 p-4 text-sm leading-6 text-cyan-50">
        {{ __('A área será identificada automaticamente com base no assunto e na descrição do problema. Descreva o máximo de contexto possível para melhorar o encaminhamento.') }}
    </div>

    <div>
        <x-input-label for="description" value="Descrição do problema" />
        <textarea
            id="description"
            name="description"
            rows="8"
            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
            required
        >{{ old('description', $ticket->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
</div>
