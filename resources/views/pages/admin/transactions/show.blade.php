@extends('layouts.admin')

@section('title', 'Detail Transaksi | Admin SN Store')
@section('breadcrumb', 'Detail transaksi')

@section('content')
    <div class="grid gap-7">
        <section>
            <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 transition hover:text-red-700 dark:text-zinc-300 dark:hover:text-red-300">
                <svg class="size-4 rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.25 10a.75.75 0 0 1 .75-.75h10.19L10.72 5.78a.75.75 0 0 1 0-1.06l4.75 4.75a.75.75 0 0 1 0 1.06l-4.75 4.75a.75.75 0 1 1-1.06-1.06l3.47-3.47H4a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/></svg>
                Kembali ke transaksi
            </a>
            <h1 class="mt-4 font-mono text-2xl font-bold tracking-tight text-zinc-950 dark:text-white">{{ $order->order_number }}</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Dibuat {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </section>

        <div class="grid items-start gap-7 lg:grid-cols-3">
            <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Status</h2>
                <dl class="mt-4 grid gap-4">
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Status pesanan</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $statusLabel }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Status pembayaran</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $paymentStatusLabel }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Status gateway</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->gateway_status ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">ID transaksi gateway</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white break-all">{{ $order->gateway_transaction_id ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Waktu transaksi gateway</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->gateway_transaction_time?->format('d/m/Y H:i') ?? '-' }}</dd></div>
                </dl>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Pembeli</h2>
                <dl class="mt-4 grid gap-4">
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Nama</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Email</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white break-all">{{ $order->customer_email }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Nomor HP</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->customer_phone }}</dd></div>
                </dl>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Pengiriman</h2>
                <dl class="mt-4 grid gap-4">
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Alamat</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white whitespace-pre-line">{{ $order->shipping_address }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Provinsi</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->shipping_province_name ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Kota/Kabupaten</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->shipping_city_name ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Kecamatan</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->shipping_district_name ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Kelurahan/Desa</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->shipping_village_name ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-zinc-500 dark:text-zinc-400">Kode pos</dt><dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">{{ $order->shipping_postal_code ?? '-' }}</dd></div>
                </dl>
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-5 py-5 sm:px-6 dark:border-zinc-800">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Rincian produk</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800/70 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 sm:px-6">Produk</th>
                            <th scope="col" class="px-5 py-3.5">SKU</th>
                            <th scope="col" class="px-5 py-3.5">Harga</th>
                            <th scope="col" class="px-5 py-3.5">Qty</th>
                            <th scope="col" class="px-5 py-3.5 text-right sm:px-6">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($order->items as $item)
                            <tr>
                                <th scope="row" class="px-5 py-4 font-medium text-zinc-900 sm:px-6 dark:text-zinc-100">{{ $item->product_name }}</th>
                                <td class="px-5 py-4 font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $item->sku }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-300">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $item->quantity }}</td>
                                <td class="px-5 py-4 text-right whitespace-nowrap font-medium text-zinc-900 sm:px-6 dark:text-zinc-100">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <dl class="ml-auto grid max-w-sm gap-2 border-t border-zinc-100 px-5 py-5 text-sm sm:px-6 dark:border-zinc-800">
                <div class="flex justify-between"><dt class="text-zinc-600 dark:text-zinc-300">Ongkos kirim @if ($order->shipping_courier)({{ strtoupper($order->shipping_courier) }} {{ $order->shipping_service }})@endif</dt><dd class="font-medium text-zinc-900 dark:text-zinc-100">Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="font-semibold text-zinc-900 dark:text-white">Total</dt><dd class="font-bold text-zinc-950 dark:text-white">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd></div>
            </dl>
        </section>
    </div>
@endsection
