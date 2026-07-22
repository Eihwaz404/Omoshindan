<div
    class="grid gap-6"
    x-data="{
        description: @js(old('description', $ticket->description ?? '')),
        minLength: @js($descriptionMinLength ?? 20),
        maxLength: @js($descriptionMaxLength ?? 1000),
    }"
>
    <div>
        <label for="subject_id" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
            <x-heroicon-o-clipboard-document-list class="h-4 w-4 text-cyan-300" />
            <span>Assunto</span>
        </label>
        <select
            id="subject_id"
            name="subject_id"
            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
            required
            autofocus
        >
            <option value="">{{ __('Selecione um assunto') }}</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected((string) old('subject_id', $ticket->subject_id ?? '') === (string) $subject->id)>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs leading-5 text-slate-500">Os assuntos ficam organizados por categoria e em ordem alfabética.</p>
        <x-input-error class="mt-2" :messages="$errors->get('subject_id')" />
    </div>

    <div>
        <label for="area_id" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
            <x-heroicon-o-building-office-2 class="h-4 w-4 text-emerald-300" />
            <span>Área de suporte</span>
        </label>
        <select
            id="area_id"
            name="area_id"
            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
            required
        >
            <option value="">Selecione uma área</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}" @selected((string) old('area_id', $ticket->area_id ?? '') === (string) $area->id)>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs leading-5 text-slate-500">Escolha a área que deve receber o ticket logo na abertura.</p>
        <x-input-error class="mt-2" :messages="$errors->get('area_id')" />
    </div>

    <div>
        <label for="priority" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
            <x-heroicon-o-bolt class="h-4 w-4 text-amber-300" />
            <span>Prioridade</span>
        </label>
        <select
            id="priority"
            name="priority"
            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
            required
        >
            @foreach ($priorities as $priorityKey => $priority)
                <option value="{{ $priorityKey }}" @selected((string) old('priority', $ticket->priority ?? \App\Models\Ticket::PRIORITY_NORMAL) === (string) $priorityKey)>
                    {{ $priority['label'] }}
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-xs leading-5 text-slate-500">A prioridade ajuda a acompanhar o prazo esperado de solução do chamado.</p>
        <x-input-error class="mt-2" :messages="$errors->get('priority')" />
    </div>

    <div>
        <label for="description" class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-200">
            <x-heroicon-o-document-text class="h-4 w-4 text-slate-300" />
            <span>Descrição do problema</span>
        </label>
        <textarea
            id="description"
            name="description"
            rows="8"
            maxlength="{{ $descriptionMaxLength ?? 1000 }}"
            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
            x-model="description"
            required
        >{{ old('description', $ticket->description ?? '') }}</textarea>
        <div class="mt-2 flex items-center justify-between gap-3 text-xs leading-5 text-slate-500">
            <p>Mínimo obrigatório: <span class="font-semibold text-slate-300" x-text="minLength"></span> caracteres. Máximo: <span class="font-semibold text-slate-300" x-text="maxLength"></span>.</p>
            <p><span class="font-semibold text-slate-300" x-text="description.length"></span> de <span class="font-semibold text-slate-300" x-text="maxLength"></span> caracteres usados.</p>
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    @include('support.tickets.partials.attachments')
</div>
