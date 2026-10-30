@extends('layouts.public')

@section('title', 'Checkout | SN Store')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">Satu langkah lagi</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Checkout</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Periksa data penerima dan ringkasan pesananmu.</p>
        </div>

        <div role="status" class="mt-6 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm leading-6 text-sky-900 dark:border-sky-900 dark:bg-sky-950/40 dark:text-sky-200">
            Pembayaran akan diproses dengan aman melalui Midtrans setelah pesanan dibuat.
        </div>
        @if ($errors->has('payment'))
            <div role="alert" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                {{ $errors->first('payment') }}
            </div>
        @endif
        @if ($errors->has('cart'))
            <div role="alert" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                {{ $errors->first('cart') }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">
            @csrf
            <div class="grid gap-6 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7 dark:border-zinc-800 dark:bg-zinc-900">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Data penerima</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Data nama dan email diambil dari akunmu. Kamu dapat menyesuaikannya untuk pesanan ini.</p>
                </div>

                <div>
                    <label for="name" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    @error('name') <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Nomor HP</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel" required class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        @error('phone') <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        @error('email') <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Alamat lengkap</label>
                    <textarea id="address" name="address" rows="4" autocomplete="street-address" required class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">{{ old('address', $user->address) }}</textarea>
                    @error('address') <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <aside class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Pesananmu</h2>
                <div class="mt-4 grid gap-4">
                    @foreach ($items as $item)
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $item->product->name }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $item->quantity }} × Rp{{ number_format((float) $item->product->price, 0, ',', '.') }}</p>
                            </div>
                            <span class="whitespace-nowrap font-medium text-zinc-900 dark:text-zinc-100">Rp{{ number_format((float) $item->product->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <span class="text-sm text-zinc-600 dark:text-zinc-300">Total</span>
                    <span class="font-bold text-zinc-950 dark:text-white">Rp{{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                </div>
                <button type="submit" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-red-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-800">Buat pesanan</button>
                <a href="{{ route('cart.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">Kembali ke keranjang</a>
            </aside>
        </form>
    </section>
@endsection
