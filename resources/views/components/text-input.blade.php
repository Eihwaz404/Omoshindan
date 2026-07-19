@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border border-slate-700 bg-slate-900/90 text-slate-100 shadow-sm shadow-slate-950/30 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/25']) }}>
