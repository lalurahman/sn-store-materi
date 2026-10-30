@extends('layouts.public')

@section('title', 'Semua Produk | SN Store')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">Katalog SN Store</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl dark:text-white">Temukan produk pilihanmu</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">Jelajahi berbagai perangkat elektronik untuk bekerja, berkreasi, dan menikmati waktu.</p>
        </div>
        @if (session('status'))
            <div role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('quantity'))
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                {{ $errors->first('quantity') }}
            </div>
        @endif

        <div class="grid items-start gap-8 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside aria-label="Filter kategori produk" class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Kategori</h2>
                    @if ($selectedCategory !== '' || $searchQuery !== '')
                        <a href="{{ route('products.index') }}" class="text-xs font-semibold text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Reset</a>
                    @endif
                </div>
                <nav class="mt-4 grid gap-1" aria-label="Kategori produk">
                    <a href="{{ route('products.index', array_filter(['q' => $searchQuery])) }}" @class([
                        'flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium transition',
                        'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => $selectedCategory === '',
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => $selectedCategory !== '',
                    ]) @if ($selectedCategory === '') aria-current="true" @endif>
                        <span>Semua kategori</span>
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('products.index', array_filter(['category' => $category->slug, 'q' => $searchQuery])) }}" @class([
                            'flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300' => $selectedCategory === $category->slug,
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => $selectedCategory !== $category->slug,
                        ]) @if ($selectedCategory === $category->slug) aria-current="true" @endif>
                            <span>{{ $category->name }}</span>
                            <span aria-hidden="true" class="text-zinc-400">›</span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            <div class="min-w-0">
                <div class="flex flex-col gap-4 border-b border-zinc-200 pb-5 dark:border-zinc-800 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-zinc-950 dark:text-white">
                            {{ $selectedCategoryName ?? 'Semua produk' }}
                        </h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $products->total() }} produk
                            @if ($searchQuery !== '')
                                untuk pencarian “{{ $searchQuery }}”
                            @endif
                        </p>
                    </div>
                    <form action="{{ route('products.index') }}" method="GET" role="search" class="flex w-full gap-2 sm:max-w-md">
                        @if ($selectedCategory !== '')
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        @endif
                        <label for="catalog-search" class="sr-only">Cari nama atau kode produk</label>
                        <input id="catalog-search" type="search" name="q" value="{{ $searchQuery }}" placeholder="Cari nama atau kode produk..." class="h-11 min-w-0 flex-1 rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-400">
                        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-red-700 px-5 text-sm font-semibold text-white transition hover:bg-red-800 active:scale-[0.98]">Cari</button>
                    </form>
                </div>

                @if ($products->isNotEmpty())
                    <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-7 sm:grid-cols-3 xl:grid-cols-4">
                        @foreach ($products as $product)
                            <article data-product-card class="group min-w-0 overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:shadow-black/20">
                                <div class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                    @if ($product->stock > 0)
                                        @auth
                                            <form method="POST" action="{{ route('cart.items.store', $product) }}" class="absolute bottom-3 right-3">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" title="Tambah ke keranjang" aria-label="Tambah {{ $product->name }} ke keranjang" class="flex size-10 items-center justify-center rounded-full bg-red-700 text-white shadow-md transition hover:scale-105 hover:bg-red-800 active:scale-95">
                                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.1a2 2 0 0 0 2 1.6h8.9a2 2 0 0 0 1.9-1.4L22 8H6" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 20h.01M18 20h.01M14 9v4m-2-2h4" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" title="Login untuk menambahkan ke keranjang" aria-label="Login untuk menambahkan {{ $product->name }} ke keranjang" class="absolute bottom-3 right-3 flex size-10 items-center justify-center rounded-full bg-white text-zinc-700 shadow-md transition hover:scale-105 hover:text-red-700 dark:bg-zinc-950 dark:text-white dark:hover:text-red-300">
                                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.1a2 2 0 0 0 2 1.6h8.9a2 2 0 0 0 1.9-1.4L22 8H6" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 20h.01M18 20h.01M14 9v4m-2-2h4" />
                                                </svg>
                                            </a>
                                        @endauth
                                    @else
                                        <span title="Stok habis" aria-label="Stok {{ $product->name }} habis" class="absolute bottom-3 right-3 flex size-10 cursor-not-allowed items-center justify-center rounded-full bg-zinc-400 text-white shadow-md dark:bg-zinc-700">
                                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.1a2 2 0 0 0 2 1.6h8.9a2 2 0 0 0 1.9-1.4L22 8H6" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 20h.01M18 20h.01M9 10l6 6m0-6-6 6" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <p class="text-xs font-medium text-red-700 dark:text-red-400">{{ $product->category->name }}</p>
                                    <h3 class="mt-1.5 min-h-10 text-sm font-semibold leading-5 text-zinc-900 dark:text-zinc-100">{{ $product->name }}</h3>
                                    <p class="mt-3 font-bold text-zinc-950 dark:text-white">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-zinc-300 px-6 py-14 text-center dark:border-zinc-700">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 text-xl text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300" aria-hidden="true">⌕</div>
                        <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">Produk tidak ditemukan</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Coba kata kunci lain atau pilih kategori yang berbeda.</p>
                        <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800">Lihat semua produk</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
