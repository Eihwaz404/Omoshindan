<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/15 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-cyan-100 transition duration-150 ease-in-out hover:bg-cyan-500/25 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-400/40 focus:ring-offset-0 active:bg-cyan-500/30']) }}>
    {{ $slot }}
</button>
