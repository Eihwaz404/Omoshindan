<div
    class="grid gap-6"
    x-data="{
        description: @js(old('description', $ticket->description ?? '')),
        minLength: @js($descriptionMinLength ?? 20),
        maxLength: @js($descriptionMaxLength ?? 1000),
    }"
>
    <div>
        <x-input-label for="subject_id" value="Assunto" />
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
        <x-input-label for="area_id" value="Área de suporte" />
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
        <x-input-label for="description" value="Descrição do problema" />
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
