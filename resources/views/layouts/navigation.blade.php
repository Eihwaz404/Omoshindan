<nav x-data="{ open: false, accessOpen: false, supportOpen: false }" class="relative z-50 border-b border-slate-800/80 bg-slate-900/90 backdrop-blur">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <x-application-logo class="block h-9 w-auto" />
                        <span class="hidden text-xs uppercase tracking-[0.22em] text-slate-400 md:inline">Omoshindan</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <div class="relative flex items-center sm:-my-px" @click.outside="supportOpen = false">
                        <button
                            type="button"
                            @click="supportOpen = !supportOpen"
                            class="inline-flex h-full items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-slate-400 transition duration-150 ease-in-out hover:border-slate-500 hover:text-slate-100 focus:border-slate-500 focus:text-slate-100 focus:outline-none"
                        >
                            {{ __('Suporte') }}
                            <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="supportOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 top-full z-[60] mt-2 w-48 overflow-hidden rounded-md border border-slate-800 bg-slate-900 shadow-xl shadow-slate-950/60"
                        >
                            <a href="{{ route('support.tickets.index') }}" class="block px-4 py-3 text-sm text-slate-200 transition hover:bg-slate-800 hover:text-white">
                                {{ __('Tickets') }}
                            </a>
                        </div>
                    </div>

                    @can('users.view')
                        <div class="relative flex items-center sm:-my-px" @click.outside="accessOpen = false">
                            <button
                                type="button"
                                @click="accessOpen = !accessOpen"
                                class="inline-flex h-full items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-slate-400 transition duration-150 ease-in-out hover:border-slate-500 hover:text-slate-100 focus:border-slate-500 focus:text-slate-100 focus:outline-none"
                            >
                                {{ __('Acesso') }}
                                <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div
                                x-cloak
                                x-show="accessOpen"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full z-[60] mt-2 w-48 overflow-hidden rounded-md border border-slate-800 bg-slate-900 shadow-xl shadow-slate-950/60"
                            >
                                <a href="{{ route('access.users.index') }}" class="block px-4 py-3 text-sm text-slate-200 transition hover:bg-slate-800 hover:text-white">
                                    {{ __('Usuários') }}
                                </a>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center rounded-md border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium leading-4 text-slate-200 transition duration-150 ease-in-out hover:border-cyan-400/50 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 focus:ring-offset-0"
                    >
                        <div>{{ Auth::user()->name }}</div>

                        <div class="ms-1">
                            <svg class="h-4 w-4 fill-current text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <div
                        x-cloak
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 z-[60] mt-2 w-52 overflow-hidden rounded-md border border-slate-800 bg-slate-900 shadow-xl shadow-slate-950/60"
                    >
                        <a
                            href="{{ route('profile.edit') }}"
                            class="block px-4 py-3 text-sm text-slate-200 transition hover:bg-slate-800 hover:text-white"
                        >
                            Profile
                        </a>

                        <div class="border-t border-slate-800/80"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 px-4 py-3 text-start text-sm text-rose-200 transition hover:bg-slate-800 hover:text-rose-100"
                            >
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h5a1 1 0 110 2H5v10h4a1 1 0 110 2H4a1 1 0 01-1-1V4zm11.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10H9a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span>Sair</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition duration-150 ease-in-out hover:bg-slate-800 hover:text-slate-200 focus:bg-slate-800 focus:outline-none focus:text-slate-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 border-t border-slate-800 bg-slate-950/95 pt-2 pb-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('users.view')
                <x-responsive-nav-link :href="route('access.users.index')" :active="request()->routeIs('access.users.*')">
                    {{ __('Usuários') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-slate-800 pt-4 pb-1">
            <div class="px-4">
                <div class="text-base font-medium text-slate-100">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 text-start text-sm font-medium text-rose-200 transition duration-150 ease-in-out hover:bg-slate-800 hover:text-rose-100 focus:bg-slate-800 focus:outline-none focus:text-rose-100"
                    >
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h5a1 1 0 110 2H5v10h4a1 1 0 110 2H4a1 1 0 01-1-1V4zm11.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10H9a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
