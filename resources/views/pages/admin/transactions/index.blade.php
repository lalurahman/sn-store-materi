@extends('layouts.admin')

@section('title', 'Transaksi | Admin SN Store')
@section('breadcrumb', 'Transaksi')

@section('content')
    <div class="grid gap-7">
        <section class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-red-700 dark:text-red-400">Operasional toko</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Transaksi</h1>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">Pesanan yang dibuat pelanggan dari halaman checkout.</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-800 dark:bg-red-950/50 dark:text-red-300">
                {{ $transactions->total() }} transaksi
            </span>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-2 border-b border-zinc-100 px-5 py-5 sm:px-6 dark:border-zinc-800">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Daftar transaksi</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Ringkasan nomor pesanan, pembeli, total belanja, dan status.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800/70 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="w-16 px-5 py-3.5 sm:px-6">#</th>
                            <th scope="col" class="px-5 py-3.5">No. transaksi</th>
                            <th scope="col" class="px-5 py-3.5">Pembeli</th>
                            <th scope="col" class="px-5 py-3.5">Tanggal</th>
                            <th scope="col" class="px-5 py-3.5">Item</th>
                            <th scope="col" class="px-5 py-3.5">Total</th>
                            <th scope="col" class="px-5 py-3.5 sm:px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($transactions as $index => $transaction)
                            <tr class="transition hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40">
                                <td class="px-5 py-4 text-zinc-500 sm:px-6 dark:text-zinc-400">{{ str_pad((string) ($transactions->firstItem() + $index), 2, '0', STR_PAD_LEFT) }}</td>
                                <th scope="row" class="px-5 py-4 font-mono text-xs font-semibold text-zinc-900 dark:text-zinc-100">{{ $transaction['number'] }}</th>
                                <td class="px-5 py-4 font-medium text-zinc-800 dark:text-zinc-200">{{ $transaction['customer'] }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-300">{{ date('d/m/Y', strtotime($transaction['date'])) }}</td>
                                <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $transaction['items'] }} item</td>
                                <td class="px-5 py-4 whitespace-nowrap font-medium text-zinc-800 dark:text-zinc-200">Rp {{ number_format($transaction['total'], 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-green-50 text-green-800 dark:bg-green-950/50 dark:text-green-300' => $transaction['status'] === 'Selesai' || $transaction['status'] === 'Dikirim',
                                        'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300' => $transaction['status'] === 'Diproses' || $transaction['status'] === 'Menunggu pembayaran',
                                        'bg-red-50 text-red-800 dark:bg-red-950/50 dark:text-red-300' => $transaction['status'] === 'Dibatalkan',
                                    ])>
                                        {{ $transaction['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="font-semibold text-zinc-900 dark:text-white">Belum ada transaksi</p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Pesanan pelanggan akan ditampilkan di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="border-t border-zinc-100 px-5 py-4 sm:px-6 dark:border-zinc-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
