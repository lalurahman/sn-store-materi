<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.transactions.index', [
            'transactions' => [
                [
                    'number' => 'INV-2026-1042',
                    'customer' => 'Raka Pratama',
                    'date' => '2026-10-21',
                    'items' => 2,
                    'total' => 1448000,
                    'status' => 'Selesai',
                ],
                [
                    'number' => 'INV-2026-1041',
                    'customer' => 'Nadia Putri',
                    'date' => '2026-10-20',
                    'items' => 1,
                    'total' => 4799000,
                    'status' => 'Diproses',
                ],
                [
                    'number' => 'INV-2026-1040',
                    'customer' => 'Dimas Saputra',
                    'date' => '2026-10-19',
                    'items' => 3,
                    'total' => 2357000,
                    'status' => 'Menunggu pembayaran',
                ],
                [
                    'number' => 'INV-2026-1039',
                    'customer' => 'Alya Maharani',
                    'date' => '2026-10-18',
                    'items' => 1,
                    'total' => 1299000,
                    'status' => 'Dibatalkan',
                ],
                [
                    'number' => 'INV-2026-1038',
                    'customer' => 'Bima Kurniawan',
                    'date' => '2026-10-17',
                    'items' => 2,
                    'total' => 954000,
                    'status' => 'Dikirim',
                ],
            ],
        ]);
    }
}
