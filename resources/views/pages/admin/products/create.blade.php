@extends('layouts.admin')

@section('title', 'Tambah Produk | Admin SN Store')
@section('breadcrumb', 'Tambah produk')

@section('content')
    <div class="grid gap-7">
        <section>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 transition hover:text-red-700 dark:text-zinc-300 dark:hover:text-red-300">
                <svg class="size-4 rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.25 10a.75.75 0 0 1 .75-.75h10.19L10.72 5.78a.75.75 0 0 1 1.06-1.06l4.75 4.75a.75.75 0 0 1 0 1.06l-4.75 4.75a.75.75 0 1 1-1.06-1.06l3.47-3.47H4a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/></svg>
                Kembali ke produk
            </a>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Tambah produk</h1>
            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Isi informasi produk untuk katalog SN Store.</p>
        </section>

        <section class="max-w-3xl rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7 dark:border-zinc-800 dark:bg-zinc-900">
            @include('pages.admin.products._form')
        </section>
    </div>
@endsection
