@extends('layouts.public')

@section('title', 'SN Store | Elektronik untuk Setiap Hari')

@section('content')
    <section class="mx-auto max-w-7xl px-4 pb-14 pt-8 sm:px-6 md:pb-20 md:pt-12 lg:px-8" aria-label="Pilihan elektronik SN Store">
        <div x-data="{ activeSlide: 0 }" class="grid items-center gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:gap-12">
            <div class="order-2 lg:order-1">
                <div class="max-w-xl">
                    <p class="mb-4 text-sm font-semibold text-red-700 dark:text-red-400">Pilihan elektronik, lebih mudah</p>
                    @foreach ($slides as $index => $slide)
                        <div x-cloak x-show="activeSlide === {{ $index }}" id="hero-slide-{{ $index }}" aria-live="polite">
                            <h1 class="text-4xl font-bold leading-tight tracking-tight text-zinc-950 sm:text-5xl dark:text-white">{{ $slide['title'] }}</h1>
                            <p class="mt-5 max-w-lg text-base leading-7 text-zinc-600 dark:text-zinc-300">{{ $slide['description'] }}</p>
                        </div>
                    @endforeach

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-full bg-red-700 px-6 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-red-800 active:scale-[0.98]">Belanja sekarang</a>
                        <a href="#kategori" class="inline-flex items-center justify-center rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-800 transition hover:border-red-300 hover:text-red-700 active:scale-[0.98] dark:border-zinc-700 dark:text-zinc-100 dark:hover:border-red-700 dark:hover:text-red-300">Jelajahi kategori</a>
                    </div>

                    <div class="mt-8 flex items-center gap-3" aria-label="Navigasi slider">
                        <button type="button" @click="activeSlide = (activeSlide + {{ count($slides) }} - 1) % {{ count($slides) }}" aria-label="Slide sebelumnya" class="flex size-10 items-center justify-center rounded-full border border-zinc-300 text-lg text-zinc-800 transition hover:border-red-600 hover:text-red-700 dark:border-zinc-700 dark:text-white dark:hover:border-red-500 dark:hover:text-red-300">‹</button>
                        @foreach ($slides as $index => $slide)
                            <button type="button" @click="activeSlide = {{ $index }}" aria-label="Tampilkan slide {{ $index + 1 }}" :aria-current="activeSlide === {{ $index }} ? 'true' : 'false'" class="h-2.5 rounded-full bg-zinc-300 transition-all aria-[current=true]:w-8 aria-[current=true]:bg-red-700 dark:bg-zinc-700 dark:aria-[current=true]:bg-red-400" :class="activeSlide === {{ $index }} ? 'w-8 bg-red-700 dark:bg-red-400' : 'w-2.5'"></button>
                        @endforeach
                        <button type="button" @click="activeSlide = (activeSlide + 1) % {{ count($slides) }}" aria-label="Slide berikutnya" class="flex size-10 items-center justify-center rounded-full border border-zinc-300 text-lg text-zinc-800 transition hover:border-red-600 hover:text-red-700 dark:border-zinc-700 dark:text-white dark:hover:border-red-500 dark:hover:text-red-300">›</button>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2">
                @foreach ($slides as $index => $slide)
                    <div x-cloak x-show="activeSlide === {{ $index }}" class="overflow-hidden rounded-2xl bg-zinc-200 dark:bg-zinc-800">
                        <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}" @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif class="aspect-[4/3] w-full object-cover sm:aspect-[16/10] lg:aspect-[5/4]">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="kategori" class="border-y border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900/40">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-950 dark:text-white">Jelajahi kategori</h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Temukan perangkat sesuai kebutuhanmu.</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-red-700 transition hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Lihat semua produk <span aria-hidden="true">→</span></a>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-5 text-sm font-semibold text-zinc-800 transition hover:border-red-300 hover:bg-red-50 hover:text-red-800 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:border-red-800 dark:hover:bg-red-950/30 dark:hover:text-red-300">
                        {{ $category->name }}
                        <span aria-hidden="true" class="ml-1 inline-block transition group-hover:translate-x-1">↗</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section id="produk" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-20 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Produk terbaru</h2>
                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">
                    @if ($selectedCategory !== '')
                        Kategori {{ $selectedCategoryName }}
                    @elseif ($searchQuery !== '')
                        Hasil pencarian untuk “{{ $searchQuery }}”
                    @else
                        Perangkat pilihan untuk melengkapi keseharianmu.
                    @endif
                </p>
            </div>
            @if ($selectedCategory !== '' || $searchQuery !== '')
                <a href="{{ route('home') }}#produk" class="text-sm font-semibold text-red-700 transition hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus filter</a>
            @endif
        </div>

        @if ($products->isNotEmpty())
            <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-5 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($products as $product)
                    <article data-product-card class="group min-w-0">
                        <div class="overflow-hidden rounded-2xl bg-zinc-100 dark:bg-zinc-900">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                        </div>
                        <div class="pt-4">
                            <p class="text-xs font-medium text-red-700 dark:text-red-400">{{ $product['category'] }}</p>
                            <h3 class="mt-1.5 min-h-11 text-sm font-semibold leading-5 text-zinc-900 dark:text-zinc-100">{{ $product['name'] }}</h3>
                            <p class="mt-2 font-bold text-zinc-950 dark:text-white">Rp{{ number_format($product['price'], 0, ',', '.') }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Produk belum ditemukan</h3>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Coba kata kunci lain atau lihat seluruh pilihan produk.</p>
                <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800">Lihat semua produk</a>
            </div>
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid items-center gap-6 rounded-2xl bg-red-700 px-6 py-9 text-white sm:px-10 md:grid-cols-[1fr_auto] md:py-11">
            <div>
                <h2 class="max-w-xl text-2xl font-bold tracking-tight sm:text-3xl">Siap menemukan perangkat berikutnya?</h2>
                <p class="mt-3 max-w-xl text-sm leading-6 text-red-100">Jelajahi pilihan elektronik yang pas untuk rutinitasmu.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex w-fit items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-red-800 transition hover:-translate-y-0.5 hover:bg-red-50 active:scale-[0.98]">Lihat produk</a>
        </div>
    </section>
@endsection
