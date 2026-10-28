@extends('layouts.admin')

@section('title', 'Kategori | Admin SN Store')
@section('breadcrumb', 'Kategori')

@section('content')
    <div class="grid gap-7">
        <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-red-700 dark:text-red-400">Katalog toko</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Kategori</h1>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Kelola kategori produk yang ditampilkan di toko.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex w-fit items-center rounded-full bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-800 dark:bg-red-950/50 dark:text-red-300">
                    {{ count($categories) }} kategori
                </span>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-red-800 active:scale-[0.98]">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                    Tambah kategori
                </a>
            </div>
        </section>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-2 border-b border-zinc-100 px-5 py-5 sm:px-6 dark:border-zinc-800">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Daftar kategori produk</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Kategori produk untuk katalog toko.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800/70 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">#</th>
                            <th scope="col" class="px-5 py-3.5">Nama kategori</th>
                            <th scope="col" class="px-5 py-3.5">Slug</th>
                            <th scope="col" class="px-5 py-3.5 sm:px-6">Deskripsi</th>
                            <th scope="col" class="px-5 py-3.5 text-right sm:px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($categories as $index => $category)
                            <tr class="transition hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40">
                                <td class="px-5 py-4 text-zinc-500 sm:px-6 dark:text-zinc-400">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</td>
                                <th scope="row" class="px-5 py-4 font-semibold text-zinc-900 dark:text-zinc-100">{{ $category['name'] }}</th>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-zinc-100 px-2 py-1 font-mono text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $category['slug'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-zinc-600 sm:px-6 dark:text-zinc-300">{{ $category['description'] ?: 'Belum ada deskripsi' }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('admin.categories.edit', $category['slug']) }}" aria-label="Edit kategori {{ $category['name'] }}" title="Edit kategori" class="flex size-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-red-50 hover:text-red-700 dark:text-zinc-400 dark:hover:bg-red-950/40 dark:hover:text-red-300">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m13.9 3.1 3 3-9.8 9.8-4.1 1.1 1.1-4.1 9.8-9.8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="m11.8 5.2 3 3M3.5 16.5l2.1-2.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category['slug']) }}" data-confirm-delete data-category-name="{{ $category['name'] }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Hapus kategori {{ $category['name'] }}" title="Hapus kategori" class="flex size-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-red-50 hover:text-red-700 dark:text-zinc-400 dark:hover:bg-red-950/40 dark:hover:text-red-300">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3.5 5.5h13M8 5.5V3.8h4v1.7m-6.8 0 .7 10.7h6.2l.7-10.7M8.5 8.5v5M11.5 8.5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="font-semibold text-zinc-900 dark:text-white">Belum ada kategori</p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Tambahkan kategori pertama untuk mengelompokkan produk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
