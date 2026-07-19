<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 shadow-sm shadow-slate-950/20 transition duration-150 ease-in-out hover:border-cyan-400/40 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 focus:ring-offset-0 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
