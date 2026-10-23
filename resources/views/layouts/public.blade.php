<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Belanja perangkat elektronik pilihan dengan mudah di SN Store.">

        <title>@yield('title', 'SN Store | Belanja Elektronik')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

        @include('components.theme-init')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div x-data="{}" class="min-h-screen">
            <header class="relative z-30 border-b border-zinc-200 bg-white/95 dark:border-zinc-800 dark:bg-zinc-950/95">
                <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-5 px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="SN Store, beranda">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-red-700 text-sm font-bold tracking-tight text-white">SN</span>
                        <span class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white">sn-store</span>
                    </a>

                    <nav class="hidden items-center gap-7 text-sm font-medium text-zinc-600 lg:flex dark:text-zinc-300" aria-label="Navigasi utama">
                        <a href="{{ route('home') }}" class="text-red-700 transition hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Beranda</a>
                        <a href="{{ route('home') }}#produk" class="transition hover:text-red-700 dark:hover:text-red-400">Produk</a>
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-controls="category-menu" class="flex items-center gap-1.5 transition hover:text-red-700 dark:hover:text-red-400">
                                Kategori
                                <span aria-hidden="true" class="text-xs">⌄</span>
                            </button>
                            <div id="category-menu" x-cloak x-show="open" @click.outside="open = false" class="absolute left-0 top-full mt-3 w-48 rounded-xl border border-zinc-200 bg-white p-2 shadow-xl shadow-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900">
                                @foreach ($categories as $category)
                                    <a href="{{ route('home', ['category' => $category]) }}" class="block rounded-lg px-3 py-2 text-sm transition hover:bg-red-50 hover:text-red-700 dark:hover:bg-zinc-800 dark:hover:text-red-300">{{ $category }}</a>
                                @endforeach
                            </div>
                        </div>
                    </nav>

                    <div class="hidden items-center gap-3 lg:flex">
                        <form action="{{ route('home') }}" method="GET" role="search" class="relative">
                            <label for="desktop-search" class="sr-only">Cari produk</label>
                            <input id="desktop-search" type="search" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari produk..." class="h-10 w-52 rounded-full border border-zinc-200 bg-zinc-50 py-2 pl-4 pr-10 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-400">
                            <button type="submit" aria-label="Cari" class="absolute right-1 top-1 flex size-8 items-center justify-center rounded-full text-zinc-500 transition hover:bg-zinc-200 hover:text-red-700 dark:hover:bg-zinc-800 dark:hover:text-red-300">
                                <span aria-hidden="true" class="text-lg leading-none">⌕</span>
                            </button>
                        </form>
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-red-700 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-red-300">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-red-700 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-red-300">Login</a>
                            <a href="{{ route('register') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-red-700 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-red-300">Register</a>
                        @endauth
                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-red-700 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-red-300">Logout</button>
                            </form>
                        @endauth
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-controls="cart-menu" class="flex items-center gap-2 rounded-full bg-red-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800 active:scale-[0.98]">
                                Keranjang
                                <span class="flex size-5 items-center justify-center rounded-full bg-white/20 text-xs">0</span>
                            </button>
                            <div id="cart-menu" x-cloak x-show="open" @click.outside="open = false" class="absolute right-0 top-full mt-3 w-64 rounded-xl border border-zinc-200 bg-white p-5 shadow-xl shadow-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900">
                                <p class="font-semibold text-zinc-900 dark:text-white">Keranjang masih kosong</p>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Pilih produk favoritmu untuk mulai berbelanja.</p>
                                <a href="{{ route('home') }}#produk" @click="open = false" class="mt-4 inline-flex rounded-full bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800">Lihat produk</a>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <x-theme-toggle />
                        <button type="button" @click="$dispatch('toggle-mobile-menu')" class="flex size-10 items-center justify-center rounded-full border border-zinc-200 text-lg text-zinc-800 transition hover:border-red-300 hover:text-red-700 lg:hidden dark:border-zinc-700 dark:text-white dark:hover:border-red-700 dark:hover:text-red-300" aria-label="Buka navigasi">
                            <span aria-hidden="true">☰</span>
                        </button>
                    </div>
                </div>

                <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-cloak x-show="open" x-transition.opacity class="border-t border-zinc-200 bg-white px-4 pb-5 pt-4 lg:hidden dark:border-zinc-800 dark:bg-zinc-950">
                    <nav class="mx-auto flex max-w-7xl flex-col gap-1" aria-label="Navigasi mobile">
                        <a href="{{ route('home') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-red-700 dark:text-red-400">Beranda</a>
                        <a href="{{ route('home') }}#produk" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-900">Produk</a>
                        <p class="px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wide text-zinc-500">Kategori</p>
                        @foreach ($categories as $category)
                            <a href="{{ route('home', ['category' => $category]) }}" class="rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-900">{{ $category }}</a>
                        @endforeach
                        <form action="{{ route('home') }}" method="GET" role="search" class="mt-3">
                            <label for="mobile-search" class="sr-only">Cari produk</label>
                            <input id="mobile-search" type="search" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari produk..." class="h-11 w-full rounded-full border border-zinc-300 bg-zinc-50 px-4 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-400">
                        </form>
                        @auth
                            <a href="{{ route('dashboard') }}" class="mt-2 rounded-full bg-red-700 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-red-800">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="mt-2 rounded-full bg-red-700 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-red-800">Login</a>
                            <a href="{{ route('register') }}" class="mt-2 rounded-full border border-zinc-300 px-4 py-3 text-center text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">Register</a>
                        @endauth
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full rounded-full border border-zinc-300 px-4 py-3 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">Logout</button>
                            </form>
                        @endauth
                        <div x-data="{ open: false }" class="mt-2">
                            <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-controls="mobile-cart-menu" class="flex w-full items-center justify-between rounded-full border border-zinc-300 px-4 py-3 text-sm font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                Keranjang
                                <span class="flex size-5 items-center justify-center rounded-full bg-red-700 text-xs text-white">0</span>
                            </button>
                            <div id="mobile-cart-menu" x-cloak x-show="open" class="rounded-xl px-4 py-3">
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">Keranjang masih kosong</p>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Pilih produk favoritmu untuk mulai berbelanja.</p>
                                <a href="{{ route('home') }}#produk" @click="open = false" class="mt-3 inline-flex rounded-full bg-red-700 px-4 py-2 text-sm font-semibold text-white">Lihat produk</a>
                            </div>
                        </div>
                    </nav>
                </div>
            </header>

            <main>
                @yield('content')
            </main>

            <footer class="border-t border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-[1.4fr_0.8fr_0.8fr] lg:px-8">
                    <div class="max-w-sm">
                        <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="SN Store, beranda">
                            <span class="flex size-10 items-center justify-center rounded-xl bg-red-700 text-sm font-bold tracking-tight text-white">SN</span>
                            <span class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white">sn-store</span>
                        </a>
                        <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Pilihan perangkat elektronik untuk mendukung aktivitas, pekerjaan, dan hiburan setiap hari.</p>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Jelajahi</h2>
                        <ul class="mt-4 grid gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                            <li><a href="{{ route('home') }}" class="transition hover:text-red-700 dark:hover:text-red-400">Beranda</a></li>
                            <li><a href="{{ route('home') }}#produk" class="transition hover:text-red-700 dark:hover:text-red-400">Produk terbaru</a></li>
                            <li><a href="{{ route('home', ['category' => 'Audio']) }}" class="transition hover:text-red-700 dark:hover:text-red-400">Kategori audio</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Hubungi kami</h2>
                        <ul class="mt-4 grid gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                            <li><a href="mailto:halo@sn-store.id" class="transition hover:text-red-700 dark:hover:text-red-400">halo@sn-store.id</a></li>
                            <li><a href="tel:+62215550120" class="transition hover:text-red-700 dark:hover:text-red-400">+62 21 5550 120</a></li>
                            <li>Senin - Sabtu, 09.00 - 18.00 WIB</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-800">
                    <div class="mx-auto max-w-7xl px-4 py-5 text-xs text-zinc-500 sm:px-6 lg:px-8 dark:text-zinc-400">
                        &copy; {{ now()->year }} SN Store. Hak cipta dilindungi.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>