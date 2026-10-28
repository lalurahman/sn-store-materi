<form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="grid gap-6">
    @csrf
    @if ($product)
        @method('PUT')
    @endif

    @if ($categories->isEmpty())
        <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">
            Tambahkan kategori sebelum membuat produk.
        </div>
    @endif

    <div class="grid gap-2">
        <label for="name" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Nama produk</label>
        <input id="name" name="name" type="text" value="{{ old('name', $product?->name) }}" maxlength="150" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Contoh: Headphone Nirkabel">
        @error('name')
            <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-2">
        <label for="sku" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">SKU</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', $product?->sku) }}" maxlength="100" required aria-invalid="{{ $errors->has('sku') ? 'true' : 'false' }}" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="Contoh: SN-AUD-2401">
        @error('sku')
            <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-2">
        <label for="category_id" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Kategori</label>
        <select id="category_id" name="category_id" required aria-invalid="{{ $errors->has('category_id') ? 'true' : 'false' }}" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
            <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-2 sm:grid-cols-2 sm:gap-5">
        <div class="grid content-start gap-2">
            <label for="price" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Harga (Rp)</label>
            <input id="price" name="price" type="number" value="{{ old('price', $product?->price) }}" min="0" max="9999999999.99" step="0.01" required aria-invalid="{{ $errors->has('price') ? 'true' : 'false' }}" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="0">
            @error('price')
                <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid content-start gap-2">
            <label for="stock" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Stok</label>
            <input id="stock" name="stock" type="number" value="{{ old('stock', $product?->stock) }}" min="0" step="1" required aria-invalid="{{ $errors->has('stock') ? 'true' : 'false' }}" class="h-11 rounded-xl border border-zinc-300 bg-white px-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-400" placeholder="0">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Status stok dibuat otomatis: 0 habis, 1–10 menipis, lebih dari 10 tersedia.</p>
            @error('stock')
                <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-3">
        <div class="grid gap-2">
            <label for="image" class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Gambar produk</label>
            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @required(!$product) aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}" class="block w-full rounded-xl border border-zinc-300 bg-white text-sm text-zinc-700 file:mr-4 file:border-0 file:bg-zinc-100 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-zinc-700 hover:file:bg-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-300 dark:file:bg-zinc-800 dark:file:text-zinc-200 dark:hover:file:bg-zinc-700">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Format JPG, PNG, atau WEBP; ukuran maksimal 2 MB. Gambar disimpan di local storage.</p>
            @error('image')
                <p class="text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        @if ($product)
            <div class="flex items-center gap-4">
                <img src="{{ $imageUrl }}" alt="Gambar {{ $product->name }}" class="size-20 rounded-xl border border-zinc-200 object-cover dark:border-zinc-700">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Pilih gambar baru untuk mengganti gambar saat ini.</p>
            </div>
        @endif
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-full border border-zinc-300 px-5 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Batal</a>
        <button type="submit" @disabled($categories->isEmpty()) class="inline-flex items-center justify-center rounded-full bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60">
            {{ $product ? 'Perbarui produk' : 'Simpan produk' }}
        </button>
    </div>
</form>
