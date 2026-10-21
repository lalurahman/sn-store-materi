<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $searchQuery = $request->query('q');
        $searchQuery = is_string($searchQuery) ? trim($searchQuery) : '';

        $selectedCategory = $request->query('category');
        $selectedCategory = is_string($selectedCategory) ? trim($selectedCategory) : '';

        $categories = [
            'Audio',
            'Smartphone',
            'Laptop',
            'Wearable',
            'Aksesori',
        ];

        $products = collect([
            [
                'name' => 'Headphone Wireless Studio',
                'category' => 'Audio',
                'price' => 1299000,
                'original_price' => 1599000,
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Smartphone Nova 5G',
                'category' => 'Smartphone',
                'price' => 7499000,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Laptop Air 14 inci',
                'category' => 'Laptop',
                'price' => 12499000,
                'original_price' => 13999000,
                'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Jam Pintar Active',
                'category' => 'Wearable',
                'price' => 1899000,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Speaker Bluetooth Portabel',
                'category' => 'Audio',
                'price' => 899000,
                'original_price' => 1099000,
                'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Kamera Mirrorless Compact',
                'category' => 'Aksesori',
                'price' => 8999000,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Mouse Wireless Ergonomis',
                'category' => 'Aksesori',
                'price' => 429000,
                'original_price' => 549000,
                'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Keyboard Mekanikal Compact',
                'category' => 'Aksesori',
                'price' => 979000,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Earbuds True Wireless',
                'category' => 'Audio',
                'price' => 799000,
                'original_price' => 999000,
                'image' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=800&q=85',
            ],
            [
                'name' => 'Monitor UltraWide 27 inci',
                'category' => 'Aksesori',
                'price' => 3299000,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=85',
            ],
        ]);

        $products = $products
            ->filter(function (array $product) use ($searchQuery, $selectedCategory): bool {
                $matchesSearch = $searchQuery === ''
                    || str_contains(
                        mb_strtolower($product['name'].' '.$product['category']),
                        mb_strtolower($searchQuery),
                    );
                $matchesCategory = $selectedCategory === ''
                    || $product['category'] === $selectedCategory;

                return $matchesSearch && $matchesCategory;
            })
            ->values();

        $slides = [
            [
                'title' => 'Teknologi terbaik untuk hidup yang lebih mudah',
                'description' => 'Temukan perangkat elektronik pilihan untuk bekerja, berkreasi, dan menikmati waktu.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1400&q=90',
                'alt' => 'Headphone wireless berwarna merah',
            ],
            [
                'title' => 'Perangkat andal untuk ide yang lebih besar',
                'description' => 'Upgrade perangkat kerja dengan pilihan laptop dan aksesori yang mendukung aktivitasmu.',
                'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1400&q=90',
                'alt' => 'Laptop terbuka di atas meja kerja',
            ],
            [
                'title' => 'Abadikan setiap momen dengan detail',
                'description' => 'Pilih kamera dan perangkat kreatif untuk menangkap cerita dari sudut pandangmu.',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1400&q=90',
                'alt' => 'Kamera digital untuk fotografi',
            ],
        ];

        return view('pages.public.home', [
            'categories' => $categories,
            'products' => $products,
            'searchQuery' => $searchQuery,
            'selectedCategory' => $selectedCategory,
            'slides' => $slides,
        ]);
    }
}
