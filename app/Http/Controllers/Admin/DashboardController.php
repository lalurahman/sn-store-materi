<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.dashboard.index', [
            'totalUsers' => User::query()->count(),
            'recentUsers' => User::query()
                ->latest()
                ->limit(5)
                ->get(['name', 'email', 'created_at']),
        ]);
    }
}
