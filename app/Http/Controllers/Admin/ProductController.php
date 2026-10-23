<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.products.index', [
            'products' => [
                [
                    'name' => 'Headphone Nirkabel Sora',
                    'sku' => 'SN-AUD-2401',
                    'category' => 'Audio',
                    'price' => 899000,
                    'stock' => 18,
                    'status' => 'Tersedia',
                ],
                [
                    'name' => 'Smartphone Nusa A5',
                    'sku' => 'SN-PHN-1806',
                    'category' => 'Smartphone',
                    'price' => 4799000,
                    'stock' => 7,
                    'status' => 'Stok menipis',
                ],
                [
                    'name' => 'Laptop Arunika 14',
                    'sku' => 'SN-LTP-0903',
                    'category' => 'Laptop',
                    'price' => 8499000,
                    'stock' => 0,
                    'status' => 'Habis',
                ],
                [
                    'name' => 'Smartwatch Rona Active',
                    'sku' => 'SN-WEA-1211',
                    'category' => 'Wearable',
                    'price' => 1299000,
                    'stock' => 12,
                    'status' => 'Tersedia',
                ],
                [
                    'name' => 'Speaker Mini Senja',
                    'sku' => 'SN-AUD-2208',
                    'category' => 'Audio',
                    'price' => 549000,
                    'stock' => 4,
                    'status' => 'Stok menipis',
                ],
            ],
        ]);
    }
}
