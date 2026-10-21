<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.categories.index', [
            'categories' => $this->categories(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.categories.create', [
            'category' => null,
        ]);
    }

    public function edit(string $slug): View
    {
        $category = collect($this->categories())->firstWhere('slug', $slug);

        abort_unless($category, 404);

        return view('pages.admin.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * @return array<int, array{name: string, slug: string, description: string}>
     */
    private function categories(): array
    {
        return [
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'description' => 'Headphone, speaker, dan perangkat audio lainnya.',
            ],
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Ponsel untuk komunikasi dan aktivitas harian.',
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Perangkat komputasi untuk kerja dan belajar.',
            ],
            [
                'name' => 'Wearable',
                'slug' => 'wearable',
                'description' => 'Jam pintar dan perangkat teknologi yang dikenakan.',
            ],
            [
                'name' => 'Aksesori',
                'slug' => 'aksesori',
                'description' => 'Aksesori pendukung perangkat elektronik.',
            ],
        ];
    }
}
