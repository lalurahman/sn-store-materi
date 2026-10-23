<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.users.index', [
            'users' => [
                [
                    'name' => 'Raka Pratama',
                    'email' => 'raka.pratama@example.test',
                    'role' => 'Pelanggan',
                    'joined_at' => '2026-10-21',
                    'status' => 'Aktif',
                ],
                [
                    'name' => 'Nadia Putri',
                    'email' => 'nadia.putri@example.test',
                    'role' => 'Pelanggan',
                    'joined_at' => '2026-10-18',
                    'status' => 'Aktif',
                ],
                [
                    'name' => 'Dimas Saputra',
                    'email' => 'dimas.saputra@example.test',
                    'role' => 'Pelanggan',
                    'joined_at' => '2026-10-12',
                    'status' => 'Aktif',
                ],
                [
                    'name' => 'Alya Maharani',
                    'email' => 'alya.maharani@example.test',
                    'role' => 'Pelanggan',
                    'joined_at' => '2026-10-08',
                    'status' => 'Nonaktif',
                ],
                [
                    'name' => 'Bima Kurniawan',
                    'email' => 'bima.kurniawan@example.test',
                    'role' => 'Pelanggan',
                    'joined_at' => '2026-10-02',
                    'status' => 'Aktif',
                ],
            ],
        ]);
    }
}
