<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-rose-400/30 bg-rose-500/15 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-rose-100 transition duration-150 ease-in-out hover:bg-rose-500/25 hover:text-white focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:ring-offset-0 active:bg-rose-500/30']) }}>
    {{ $slot }}
</button>
