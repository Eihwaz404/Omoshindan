<div class="grid gap-6">
    <div>
        <x-input-label for="subject" value="Assunto" />
        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" :value="old('subject', $ticket->subject ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('subject')" />
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
            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950/80 text-slate-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400"
            required
        >{{ old('description', $ticket->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
</div>
