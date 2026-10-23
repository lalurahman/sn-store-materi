@extends('layouts.admin')

@section('title', 'Dashboard Admin | SN Store')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="grid gap-7">
        <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-red-700 dark:text-red-400">Ringkasan toko</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Dashboard</h1>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Selamat datang kembali, {{ auth()->user()->name }}. Berikut ringkasan pengelolaan SN Store.</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex w-fit items-center gap-2 rounded-full border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">
                Lihat toko
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 0 1.06 0l6.97-6.97v4.44a.75.75 0 0 0 1.5 0v-6.25a.75.75 0 0 0-.75-.75H7.75a.75.75 0 0 0 0 1.5h4.44l-6.97 6.97a.75.75 0 0 0 0 1.06Z" clip-rule="evenodd"/></svg>
            </a>
        </section>

        <section aria-label="Statistik toko" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Total produk</p>
                        <p class="mt-4 text-xl font-bold tracking-tight text-zinc-950 dark:text-white">Data contoh</p>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3.5 7 8.5-4 8.5 4v10L12 21l-8.5-4V7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3.8 7.2 8.2 4 8.2-4M12 11.5V21" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Pratinjau produk tersedia di menu Produk</p>
            </article>

            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Kategori</p>
                        <p class="mt-4 text-xl font-bold tracking-tight text-zinc-950 dark:text-white">Data contoh</p>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Pratinjau kategori tersedia di menu Kategori</p>
            </article>

            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Transaksi</p>
                        <p class="mt-4 text-xl font-bold tracking-tight text-zinc-950 dark:text-white">Data contoh</p>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h10M4 17h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="18" cy="16" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Pratinjau transaksi tersedia di menu Transaksi</p>
            </article>

            <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Pengguna terdaftar</p>
                        <p class="mt-4 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">{{ number_format($totalUsers, 0, ',', '.') }}</p>
                    </div>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 20v-1.5a6 6 0 0 1 12 0V20H3ZM16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a4.5 4.5 0 0 1 3 4.2V20h-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">Akun yang terdaftar di toko</p>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.5fr_0.85fr]">
            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-5 sm:px-6 dark:border-zinc-800">
                    <div>
                        <h2 class="font-semibold text-zinc-950 dark:text-white">Pengguna terbaru</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Akun yang baru bergabung dengan SN Store.</p>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">5 terakhir</span>
                </div>

                @if ($recentUsers->isNotEmpty())
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($recentUsers as $user)
                            <li class="flex items-center gap-3 px-5 py-4 sm:px-6">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-sm font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                </div>
                                <time class="hidden shrink-0 text-xs text-zinc-500 sm:block dark:text-zinc-400" datetime="{{ $user->created_at->toDateString() }}">{{ $user->created_at->format('d/m/Y') }}</time>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-6 py-12 text-center">
                        <span class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 20v-1.5a6 6 0 0 1 12 0V20H3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M18 8v6M15 11h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white">Belum ada pengguna</h3>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Pengguna baru akan muncul di sini setelah mendaftar.</p>
                    </div>
                @endif
            </div>

            <aside class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Modul toko</h2>
                <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">Produk, kategori, dan transaksi ditampilkan sebagai data contoh.</p>

                <div class="mt-5 grid gap-3">
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/70">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-white text-red-700 dark:bg-zinc-900 dark:text-red-300">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m3 12 9 5 9-5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="flex-1 text-sm font-medium text-zinc-800 dark:text-zinc-200">Katalog & kategori</span>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Data contoh</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/70">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-white text-red-700 dark:bg-zinc-900 dark:text-red-300">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h10M4 17h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="18" cy="16" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
                        </span>
                        <span class="flex-1 text-sm font-medium text-zinc-800 dark:text-zinc-200">Transaksi</span>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Data contoh</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-between gap-3 rounded-xl border border-zinc-200 px-4 py-3 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">
                        Kelola profil admin
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 0 1.06 0l6.97-6.97v4.44a.75.75 0 0 0 1.5 0v-6.25a.75.75 0 0 0-.75-.75H7.75a.75.75 0 0 0 0 1.5h4.44l-6.97 6.97a.75.75 0 0 0 0 1.06Z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </aside>
        </section>
    </div>
@endsection
