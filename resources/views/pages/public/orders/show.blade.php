@extends('layouts.public')

@section('title', 'Pesanan '.$order->order_number.' | SN Store')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 sm:p-9 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex size-12 items-center justify-center rounded-full bg-amber-100 text-xl text-amber-800 dark:bg-amber-950/60 dark:text-amber-300" aria-hidden="true">…</div>
            <p class="mt-6 text-sm font-semibold text-red-700 dark:text-red-400">Pesanan berhasil dibuat</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">
                @if ($order->payment_status === 'paid')
                    Pembayaran terverifikasi
                @elseif ($order->payment_status === 'failed')
                    Pembayaran gagal atau kedaluwarsa
                @else
                    Menunggu pembayaran
                @endif
            </h1>
            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                @if ($order->payment_status === 'paid')
                    Pembayaran telah dikonfirmasi oleh Midtrans.
                @elseif ($order->payment_status === 'failed')
                    Pembayaran tidak berhasil. Silakan buat pesanan baru jika ingin mencoba kembali.
                @else
                    Selesaikan pembayaran melalui Midtrans. Status pesanan akan diperbarui setelah notifikasi pembayaran diterima.
                @endif
            </p>

            <dl class="mt-7 grid gap-4 rounded-2xl bg-zinc-50 p-4 sm:grid-cols-2 dark:bg-zinc-950">
                <div>
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Nomor pesanan</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-zinc-900 dark:text-white">{{ $order->order_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Status pembayaran</dt>
                    <dd class="mt-1 text-sm font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-700 dark:text-emerald-300' : ($order->payment_status === 'failed' ? 'text-red-700 dark:text-red-300' : 'text-amber-800 dark:text-amber-300') }}">
                        {{ $order->payment_status === 'paid' ? 'Lunas' : ($order->payment_status === 'failed' ? 'Gagal' : 'Menunggu pembayaran') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Penerima</dt>
                    <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Total pesanan</dt>
                    <dd class="mt-1 text-sm font-bold text-zinc-900 dark:text-white">Rp{{ number_format((float) $order->total, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Nomor HP</dt>
                    <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer_phone }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Email</dt>
                    <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer_email }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">Alamat pengiriman</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm font-medium text-zinc-900 dark:text-white">{{ $order->shipping_address }}</dd>
                </div>
            </dl>

            <div class="mt-7">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Rincian produk</h2>
                <div class="mt-3 divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $item->product_name }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $item->quantity }} × Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="whitespace-nowrap font-semibold text-zinc-900 dark:text-white">Rp{{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($order->payment_status === 'pending' && $order->snap_token)
                <div class="mt-7 flex flex-wrap items-center gap-3">
                    <button id="pay-order" type="button" class="inline-flex rounded-full bg-red-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-800">
                        Bayar dengan Midtrans
                    </button>
                    <a href="{{ $order->payment_url }}" class="inline-flex rounded-full border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300">
                        Buka halaman pembayaran
                    </a>
                </div>
                <p id="payment-error" role="status" class="mt-3 hidden text-sm text-red-700 dark:text-red-400"></p>
                <script src="{{ config('services.midtrans.snap_js_url') }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
                <script>
                    const paymentResultUrl = @js(route('orders.show', $order->order_number));
                    const paymentError = document.getElementById('payment-error');

                    document.getElementById('pay-order').addEventListener('click', () => {
                        if (!window.snap) {
                            paymentError.textContent = 'Midtrans Snap tidak dapat dimuat. Gunakan tautan halaman pembayaran.';
                            paymentError.classList.remove('hidden');

                            return;
                        }

                        window.snap.pay(@js($order->snap_token), {
                            onSuccess: () => window.location.assign(paymentResultUrl),
                            onPending: () => window.location.assign(paymentResultUrl),
                            onError: () => {
                                paymentError.textContent = 'Pembayaran belum berhasil. Silakan coba kembali.';
                                paymentError.classList.remove('hidden');
                            },
                        });
                    });
                </script>
            @endif

            <a href="{{ route('products.index') }}" class="mt-7 inline-flex rounded-full bg-red-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-800">Kembali belanja</a>
        </div>
    </section>
@endsection
