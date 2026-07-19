<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta name="theme-color" content="#020617">
        <link rel="icon" type="image/webp" href="/images/brand/logo-nome.webp">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100">
        <div class="min-h-screen bg-slate-950">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.14),transparent_30%)]"></div>

            <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8 sm:px-6">
                <div class="mb-6 flex items-center gap-3 text-slate-200">
                    <a href="/" class="rounded-xl border border-cyan-400/20 bg-slate-900/80 px-4 py-3 shadow-lg shadow-cyan-950/20 backdrop-blur">
                        <x-application-logo class="h-12 w-32" />
                    </a>
                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-cyan-300/80">Omoshindan</p>
                        <p class="text-sm text-slate-400">Portal de suporte e operação</p>
                    </div>
                </div>

                <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-2xl shadow-cyan-950/10 ring-1 ring-white/5 backdrop-blur lg:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
