<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f4f4f5">

        <title>@yield('title', 'Dashboard Admin | SN Store')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

        @include('components.theme-init')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-100 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div x-data="{ sidebarOpen: false, accountOpen: false }" @keydown.escape.window="sidebarOpen = false; accountOpen = false" class="min-h-screen md:flex">
            <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-zinc-950/50 md:hidden" aria-hidden="true"></div>

            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 flex h-dvh w-72 flex-col border-r border-zinc-200 bg-white transition-transform duration-200 dark:border-zinc-800 dark:bg-zinc-900 md:sticky md:top-0 md:z-auto md:w-64 md:translate-x-0">
                <div class="flex h-[72px] shrink-0 items-center justify-between border-b border-zinc-100 px-6 dark:border-zinc-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" aria-label="SN Store Admin, dashboard">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-red-700 text-sm font-bold tracking-tight text-white">SN</span>
                        <span>
                            <span class="block text-base font-bold tracking-tight text-zinc-950 dark:text-white">SN Store</span>
                            <span class="block text-xs text-zinc-500 dark:text-zinc-400">Admin panel</span>
                        </span>
                    </a>
                    <button type="button" @click="sidebarOpen = false" class="flex size-9 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 md:hidden dark:hover:bg-zinc-800" aria-label="Tutup navigasi">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-4 py-6" aria-label="Navigasi admin">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-zinc-400">Menu utama</p>
                    <div class="mt-3 grid gap-1">
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => request()->routeIs('dashboard'),
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! request()->routeIs('dashboard'),
                        ]) aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.categories.index') }}" @click="sidebarOpen = false" @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => request()->routeIs('admin.categories.*'),
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! request()->routeIs('admin.categories.*'),
                        ]) aria-current="{{ request()->routeIs('admin.categories.*') ? 'page' : 'false' }}">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Kategori
                        </a>
                        <a href="{{ route('admin.products.index') }}" @click="sidebarOpen = false" @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => request()->routeIs('admin.products.*'),
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! request()->routeIs('admin.products.*'),
                        ]) aria-current="{{ request()->routeIs('admin.products.*') ? 'page' : 'false' }}">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3.5 7 8.5-4 8.5 4v10L12 21l-8.5-4V7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3.8 7.2 8.2 4 8.2-4M12 11.5V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            Produk
                        </a>
                        <a href="{{ route('admin.transactions.index') }}" @click="sidebarOpen = false" @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => request()->routeIs('admin.transactions.*'),
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! request()->routeIs('admin.transactions.*'),
                        ]) aria-current="{{ request()->routeIs('admin.transactions.*') ? 'page' : 'false' }}">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h10M4 17h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="18" cy="16" r="3" stroke="currentColor" stroke-width="1.7"/><path d="m16.8 16 1 1 1.6-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Transaksi
                        </a>
                        <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false" @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => request()->routeIs('admin.users.*'),
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! request()->routeIs('admin.users.*'),
                        ]) aria-current="{{ request()->routeIs('admin.users.*') ? 'page' : 'false' }}">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 20v-1.5a6 6 0 0 1 12 0V20H3ZM16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a4.5 4.5 0 0 1 3 4.2V20h-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            User
                        </a>
                    </div>
                </nav>

                <div class="border-t border-zinc-100 p-4 dark:border-zinc-800">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 5h5v14h-5M10 8l4 4-4 4M14 12H4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Kembali ke toko
                    </a>
                </div>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="sticky top-0 z-20 flex h-[72px] items-center justify-between border-b border-zinc-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-10 dark:border-zinc-800 dark:bg-zinc-950/90">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="sidebarOpen = true" class="flex size-10 items-center justify-center rounded-xl border border-zinc-200 text-zinc-700 transition hover:border-red-300 hover:text-red-700 md:hidden dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300" aria-label="Buka navigasi">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">SN Store / <span class="text-zinc-700 dark:text-zinc-200">@yield('breadcrumb', 'Dashboard')</span></p>
                            <p class="mt-0.5 text-sm font-semibold text-zinc-900 dark:text-white">Panel admin</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-theme-toggle />
                        <div class="relative" @click.outside="accountOpen = false">
                            <button type="button" @click="accountOpen = !accountOpen" :aria-expanded="accountOpen.toString()" aria-controls="admin-account-menu" class="flex items-center gap-3 rounded-full p-1.5 pr-2 transition hover:bg-zinc-100 dark:hover:bg-zinc-900">
                                <span class="flex size-9 items-center justify-center rounded-full bg-red-100 text-sm font-bold text-red-800 dark:bg-red-950 dark:text-red-300">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                <span class="hidden max-w-40 text-left sm:block">
                                    <span class="block truncate text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ auth()->user()->name }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">Administrator</span>
                                </span>
                                <svg class="hidden size-4 text-zinc-500 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </button>
                            <div id="admin-account-menu" x-cloak x-show="accountOpen" x-transition.opacity class="absolute right-0 top-full mt-2 w-52 rounded-xl border border-zinc-200 bg-white p-2 shadow-lg shadow-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900">
                                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 transition hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">Pengaturan akun</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-700 transition hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-950/40">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-[1440px] px-4 py-7 sm:px-6 sm:py-9 lg:px-10">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
