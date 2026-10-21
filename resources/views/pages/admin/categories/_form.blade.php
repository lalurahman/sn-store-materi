@php
    $formCategory = $category ?? ['name' => '', 'slug' => '', 'description' => ''];
@endphp

<form class="grid gap-6" onsubmit="return false;">
    <div role="status" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-5 text-amber-900 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">
        Ini tampilan formulir. Penyimpanan kategori belum diaktifkan.
    </div>

    <div class="grid gap-2">
        <label for="name" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Nama kategori</label>
        <input id="name" name="name" type="text" value="{{ old('name', $formCategory['name']) }}" maxlength="100" autocomplete="off" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Contoh: Audio">
    </div>

    <div class="grid gap-2">
        <label for="slug" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $formCategory['slug']) }}" maxlength="120" autocomplete="off" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Contoh: audio">
        <p class="text-xs text-zinc-500 dark:text-zinc-400">Gunakan huruf, angka, tanda hubung, atau garis bawah.</p>
    </div>

    <div class="grid gap-2">
        <label for="description" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Deskripsi <span class="font-normal text-zinc-500">(opsional)</span></label>
        <textarea id="description" name="description" rows="4" maxlength="1000" class="rounded-xl border border-zinc-300 bg-white px-3.5 py-3 text-sm leading-6 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Jelaskan kategori produk ini...">{{ old('description', $formCategory['description']) }}</textarea>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-full border border-zinc-300 px-5 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Batal</a>
        <button type="button" disabled class="inline-flex cursor-not-allowed items-center justify-center rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white opacity-60">
            Simpan kategori
        </button>
    </div>
</form>
