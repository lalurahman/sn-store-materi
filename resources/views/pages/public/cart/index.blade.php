@extends('layouts.public')

@section('title', 'Keranjang | SN Store')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">Belanjaanmu</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Keranjang</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $itemCount }} item di keranjang</p>
        </div>

        @if (session('status'))
            <div role="status" class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('cart') || $errors->has('quantity'))
            <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                {{ $errors->first('cart') ?: $errors->first('quantity') }}
            </div>
        @endif

        @if ($items->isNotEmpty())
            <div class="mt-8 grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
                <div class="grid gap-4">
                    @foreach ($items as $item)
                        <article class="flex flex-col gap-4 rounded-2xl border border-zinc-200 bg-white p-4 sm:flex-row sm:items-center dark:border-zinc-800 dark:bg-zinc-900">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->product->image_path) }}" alt="{{ $item->product->name }}" class="aspect-square size-24 rounded-xl bg-zinc-100 object-cover dark:bg-zinc-800">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-red-700 dark:text-red-400">{{ $item->product->category->name }}</p>
                                <h2 class="mt-1 font-semibold text-zinc-950 dark:text-white">{{ $item->product->name }}</h2>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Stok tersedia: {{ $item->product->stock }}</p>
                                <p class="mt-2 font-bold text-zinc-950 dark:text-white">Rp{{ number_format((float) $item->product->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center justify-between gap-5 sm:flex-col sm:items-end">
                                <form method="POST" action="{{ route('cart.items.update', $item) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <label for="quantity-{{ $item->id }}" class="sr-only">Jumlah {{ $item->product->name }}</label>
                                    <input id="quantity-{{ $item->id }}" name="quantity" type="number" min="1" max="{{ max(1, $item->product->stock) }}" value="{{ $item->quantity }}" class="h-10 w-20 rounded-lg border border-zinc-300 bg-white px-2 text-center text-sm text-zinc-900 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                                    <button type="submit" class="h-10 rounded-lg border border-zinc-300 px-3 text-xs font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">Perbarui</button>
                                </form>
                                <form method="POST" action="{{ route('cart.items.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Ringkasan belanja</h2>
                    <div class="mt-5 flex items-center justify-between border-t border-zinc-200 pt-4 text-sm dark:border-zinc-800">
                        <span class="text-zinc-600 dark:text-zinc-300">Subtotal</span>
                        <span class="font-bold text-zinc-950 dark:text-white">Rp{{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-zinc-500 dark:text-zinc-400">Total akhir dapat disesuaikan saat biaya pengiriman tersedia.</p>
                    <a href="{{ route('checkout.create') }}" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-red-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-800">Lanjut ke checkout</a>
                    <a href="{{ route('products.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">Lanjut belanja</a>
                </aside>
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-dashed border-zinc-300 px-6 py-14 text-center dark:border-zinc-700">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Keranjangmu masih kosong</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Pilih produk yang kamu butuhkan untuk mulai berbelanja.</p>
                <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800">Jelajahi produk</a>
            </div>
        @endif
    </section>
@endsection
