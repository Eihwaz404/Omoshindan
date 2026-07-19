<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Omoshindan') }}</title>
        <meta name="theme-color" content="#020617">
        <link rel="icon" type="image/webp" href="/images/brand/logo-nome.webp">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100">
        <main class="min-h-screen bg-slate-950 px-6 py-10">
            <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-5xl flex-col justify-center rounded-3xl border border-slate-800 bg-slate-900/80 px-8 py-12 shadow-2xl shadow-cyan-950/20">
                <div class="flex items-center gap-3">
                    <img src="/images/brand/logo-nome.webp" alt="Omoshindan" class="h-12 w-32 object-contain">
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/80">Omoshindan</p>
                </div>
                <h1 class="mt-4 text-4xl font-semibold text-slate-50 sm:text-5xl">Portal de chamados e suporte em operação.</h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                    Ambiente centralizado para abertura, triagem e comunicação entre usuários, analistas e desenvolvimento.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-md border border-cyan-400/30 bg-cyan-500/15 px-4 py-2 text-sm font-semibold text-cyan-100 hover:bg-cyan-500/25">
                        Acessar sistema
                    </a>
                </div>
            </div>
        </main>
    </body>
</html>
