<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categoryService = app(CategoryService::class);
        return view('pages.admin.categories.index', [
            'categories' => $categoryService->getAll(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.categories.create', [
            'category' => null,
        ]);
    }

    public function store(CategoryRequest $request, CategoryService $categoryService): RedirectResponse
    {
        $categoryService->create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(string $slug, CategoryService $categoryService): View
    {
        return view('pages.admin.categories.edit', [
            'category' => $categoryService->findBySlug($slug),
        ]);
    }

    public function update(
        CategoryRequest $request,
        string $slug,
        CategoryService $categoryService
    ): RedirectResponse {
        $category = $categoryService->findBySlug($slug);
        $categoryService->update($category, $request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(string $slug, CategoryService $categoryService): RedirectResponse
    {
        $categoryService->delete($categoryService->findBySlug($slug));

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
