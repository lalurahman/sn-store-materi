@php
    $formCategory = $category ?? ['name' => '', 'description' => ''];
@endphp

<form method="POST" action="{{ $category ? route('admin.categories.update', $category['slug']) : route('admin.categories.store') }}" class="grid gap-6">
    @csrf
    @if ($category)
        @method('PUT')
    @endif

    <div class="grid gap-2">
        <label for="name" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Nama kategori</label>
        <input id="name" name="name" type="text" value="{{ old('name', $formCategory['name']) }}" maxlength="100" autocomplete="off" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Contoh: Audio">
        @error('name')
            <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <p class="text-xs text-zinc-500 dark:text-zinc-400">
        Slug akan dibuat otomatis dari nama kategori{{ $category ? '; slug saat ini: '.$category['slug'] : '' }}.
    </p>

    <div class="grid gap-2">
        <label for="description" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Deskripsi <span class="font-normal text-zinc-500">(opsional)</span></label>
        <textarea id="description" name="description" rows="4" maxlength="1000" aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}" class="rounded-xl border border-zinc-300 bg-white px-3.5 py-3 text-sm leading-6 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Jelaskan kategori produk ini...">{{ old('description', $formCategory['description']) }}</textarea>
        @error('description')
            <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-full border border-zinc-300 px-5 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Batal</a>
        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800 active:scale-[0.98]">
            {{ $category ? 'Perbarui kategori' : 'Simpan kategori' }}
        </button>
    </div>
</form>
