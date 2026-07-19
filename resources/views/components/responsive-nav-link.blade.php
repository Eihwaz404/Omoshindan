@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-cyan-400 bg-slate-800/80 py-2 pe-4 ps-3 text-start text-base font-medium text-cyan-100 transition duration-150 ease-in-out focus:outline-none'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-slate-400 transition duration-150 ease-in-out hover:border-slate-600 hover:bg-slate-800/60 hover:text-slate-100 focus:border-slate-600 focus:bg-slate-800/60 focus:text-slate-100 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
