@extends('layouts.public')

@section('title', 'Checkout | SN Store')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">Satu langkah lagi</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 dark:text-white">Checkout</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Periksa data penerima dan ringkasan pesananmu.</p>
        </div>

        <div
            role="status"
            class="mt-6 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm leading-6 text-sky-900 dark:border-sky-900 dark:bg-sky-950/40 dark:text-sky-200"
        >
            Pembayaran akan diproses dengan aman melalui Midtrans setelah pesanan dibuat.
        </div>
        @if ($errors->has('payment'))
            <div
                role="alert"
                class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
            >
                {{ $errors->first('payment') }}
            </div>
        @endif
        @if ($errors->has('cart'))
            <div
                role="alert"
                class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
            >
                {{ $errors->first('cart') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('checkout.store') }}"
            class="mt-8 grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_360px]"
        >
            @csrf
            <div
                class="grid gap-6 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7 dark:border-zinc-800 dark:bg-zinc-900">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Data penerima</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Data nama dan email diambil dari akunmu. Kamu
                        dapat menyesuaikannya untuk pesanan ini.</p>
                </div>

                <div>
                    <label
                        for="name"
                        class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                    >Nama lengkap</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        autocomplete="name"
                        required
                        class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            for="phone"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Nomor HP</label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone', $user->phone) }}"
                            autocomplete="tel"
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            for="email"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            autocomplete="email"
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label
                        for="address"
                        class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                    >Alamat lengkap</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="4"
                        autocomplete="street-address"
                        required
                        class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                    >{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            for="province_code"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Provinsi</label>
                        <select
                            id="province_code"
                            name="province_code"
                            data-region-select
                            data-region-child="city_code"
                            data-region-url="{{ route('checkout.regions.cities', ['provinceCode' => '__CODE__']) }}"
                            data-region-placeholder="Pilih kabupaten/kota"
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                        >
                            <option value="">Pilih provinsi</option>
                            @foreach ($provinces as $province)
                                <option
                                    value="{{ $province->code }}"
                                    @selected(old('province_code') === $province->code)
                                >{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_code')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            for="city_code"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Kabupaten/kota</label>
                        <select
                            id="city_code"
                            name="city_code"
                            @disabled($cities->isEmpty())
                            data-region-select
                            data-region-child="district_code"
                            data-region-url="{{ route('checkout.regions.districts', ['cityCode' => '__CODE__']) }}"
                            data-region-placeholder="Pilih kecamatan"
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 disabled:cursor-not-allowed disabled:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:disabled:bg-zinc-800"
                        >
                            <option value="">Pilih kabupaten/kota</option>
                            @foreach ($cities as $city)
                                <option
                                    value="{{ $city->code }}"
                                    @selected(old('city_code') === $city->code)
                                >{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_code')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            for="district_code"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Kecamatan</label>
                        <select
                            id="district_code"
                            name="district_code"
                            @disabled($districts->isEmpty())
                            data-region-select
                            data-region-child="village_code"
                            data-region-url="{{ route('checkout.regions.villages', ['districtCode' => '__CODE__']) }}"
                            data-region-placeholder="Pilih desa/kelurahan"
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 disabled:cursor-not-allowed disabled:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:disabled:bg-zinc-800"
                        >
                            <option value="">Pilih kecamatan</option>
                            @foreach ($districts as $district)
                                <option
                                    value="{{ $district->code }}"
                                    @selected(old('district_code') === $district->code)
                                >{{ $district->name }}</option>
                            @endforeach
                        </select>
                        @error('district_code')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            for="village_code"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Desa/kelurahan</label>
                        <select
                            id="village_code"
                            name="village_code"
                            @disabled($villages->isEmpty())
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 disabled:cursor-not-allowed disabled:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:disabled:bg-zinc-800"
                        >
                            <option value="">Pilih desa/kelurahan</option>
                            @foreach ($villages as $village)
                                <option
                                    value="{{ $village->code }}"
                                    @selected(old('village_code') === $village->code)
                                >{{ $village->name }}</option>
                            @endforeach
                        </select>
                        @error('village_code')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            for="postal_code"
                            class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                        >Kode pos</label>
                        <input
                            id="postal_code"
                            name="postal_code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="postal-code"
                            maxlength="5"
                            pattern="[0-9]{5}"
                            value="{{ old('postal_code') }}"
                            required
                            class="mt-2 h-11 w-full rounded-xl border border-zinc-300 bg-white px-4 text-sm text-zinc-900 outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
                        >
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div id="shipping-section">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Pengiriman</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Dikirim dari Kota Makassar. Pilih desa/kelurahan untuk melihat ongkos kirim.</p>
                    <p
                        id="shipping-status"
                        role="status"
                        aria-live="polite"
                        class="mt-3 text-sm text-zinc-500 dark:text-zinc-400"
                    >Pilih alamat pengiriman terlebih dahulu.</p>
                    <div id="shipping-options" class="mt-3 grid gap-2"></div>
                    @error('shipping_option')
                        <p class="mt-2 text-sm text-red-700 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <p
                    id="region-load-error"
                    role="status"
                    aria-live="polite"
                    class="hidden text-sm text-red-700 dark:text-red-400"
                >Wilayah gagal dimuat. Silakan pilih ulang.</p>
            </div>

            <aside class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Pesananmu</h2>
                <div class="mt-4 grid gap-4">
                    @foreach ($items as $item)
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $item->product->name }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $item->quantity }} ×
                                    Rp{{ number_format((float) $item->product->price, 0, ',', '.') }}</p>
                            </div>
                            <span
                                class="whitespace-nowrap font-medium text-zinc-900 dark:text-zinc-100">Rp{{ number_format((float) $item->product->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 grid gap-2 border-t border-zinc-200 pt-4 text-sm dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-600 dark:text-zinc-300">Subtotal</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">Rp{{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-600 dark:text-zinc-300">Ongkos kirim</span>
                        <span id="summary-shipping" class="font-medium text-zinc-900 dark:text-zinc-100">-</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-zinc-200 pt-3 dark:border-zinc-800">
                        <span class="text-zinc-600 dark:text-zinc-300">Total</span>
                        <span
                            id="summary-total"
                            data-subtotal="{{ (int) $subtotal }}"
                            class="font-bold text-zinc-950 dark:text-white">Rp{{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                <button
                    id="checkout-submit"
                    type="submit"
                    class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-red-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-800"
                >Buat pesanan</button>
                <a
                    href="{{ route('cart.index') }}"
                    class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-700 transition hover:border-red-300 hover:text-red-700 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300"
                >Kembali ke keranjang</a>
            </aside>
        </form>
    </section>
    <script>
        (() => {
            const selects = document.querySelectorAll('[data-region-select]');
            const errorMessage = document.getElementById('region-load-error');

            selects.forEach((parent) => {
                parent.addEventListener('change', async () => {
                    let childId = parent.dataset.regionChild;
                    let child = document.getElementById(childId);

                    while (child) {
                        const nextChildId = child.dataset.regionChild;
                        child.replaceChildren(new Option(child.dataset.regionPlaceholder ?? child
                            .options[0].text, ''));
                        child.disabled = true;
                        child = nextChildId ? document.getElementById(nextChildId) : null;
                    }

                    errorMessage.classList.add('hidden');

                    if (!parent.value) {
                        return;
                    }

                    child = document.getElementById(parent.dataset.regionChild);

                    try {
                        const endpoint = parent.dataset.regionUrl.replace('__CODE__',
                            encodeURIComponent(parent.value));
                        const response = await fetch(endpoint, {
                            headers: {
                                Accept: 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Region lookup failed.');
                        }

                        const regions = await response.json();

                        regions.forEach((region) => child.add(new Option(region.name, region
                        .code)));
                        child.disabled = regions.length === 0;
                    } catch {
                        errorMessage.classList.remove('hidden');
                    }
                });
            });
        })();
    </script>

    <script>
        (() => {
            const village = document.getElementById('village_code');
            const status = document.getElementById('shipping-status');
            const list = document.getElementById('shipping-options');
            const summaryShipping = document.getElementById('summary-shipping');
            const summaryTotal = document.getElementById('summary-total');
            const submit = document.getElementById('checkout-submit');
            const subtotal = Number(summaryTotal.dataset.subtotal);
            const oldOption = @js(old('shipping_option'));
            const url = @js(route('checkout.shipping-costs'));
            const csrf = document.querySelector('input[name="_token"]').value;
            const rupiah = (n) => 'Rp' + n.toLocaleString('id-ID');
            let requestId = 0;

            const reset = (message) => {
                requestId++;
                list.replaceChildren();
                status.textContent = message;
                status.classList.remove('text-red-700', 'dark:text-red-400');
                summaryShipping.textContent = '-';
                summaryTotal.textContent = rupiah(subtotal);
                submit.disabled = true;
                submit.classList.add('opacity-60', 'cursor-not-allowed');
            };

            const select = (option) => {
                summaryShipping.textContent = rupiah(option.cost);
                summaryTotal.textContent = rupiah(subtotal + option.cost);
                submit.disabled = false;
                submit.classList.remove('opacity-60', 'cursor-not-allowed');
            };

            const load = async () => {
                if (!village.value) {
                    reset('Pilih alamat pengiriman terlebih dahulu.');
                    return;
                }

                reset('Menghitung ongkos kirim...');
                const current = requestId;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ village_code: village.value })
                    });
                    const data = await response.json();

                    if (current !== requestId) {
                        return;
                    }

                    if (!response.ok) {
                        reset(data.message ?? 'Ongkos kirim gagal dihitung.');
                        status.classList.add('text-red-700', 'dark:text-red-400');
                        return;
                    }

                    status.textContent = 'Pilih layanan pengiriman:';
                    data.options.forEach((option, index) => {
                        const label = document.createElement('label');
                        label.className = 'flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-zinc-300 px-4 py-3 text-sm dark:border-zinc-700';
                        const input = document.createElement('input');
                        input.type = 'radio';
                        input.name = 'shipping_option';
                        input.value = option.key;
                        input.required = true;
                        input.className = 'accent-red-700';
                        input.addEventListener('change', () => select(option));
                        const text = document.createElement('span');
                        text.className = 'flex-1 text-zinc-800 dark:text-zinc-200';
                        text.textContent = option.courier_name + ' ' + option.service
                            + (option.etd ? ' (' + option.etd + ')' : '');
                        const price = document.createElement('span');
                        price.className = 'font-semibold text-zinc-900 dark:text-white';
                        price.textContent = rupiah(option.cost);
                        label.append(input, text, price);
                        list.append(label);

                        if (option.key === oldOption) {
                            input.checked = true;
                            select(option);
                        }
                    });
                } catch {
                    if (current === requestId) {
                        reset('Ongkos kirim gagal dihitung. Silakan pilih ulang alamat.');
                        status.classList.add('text-red-700', 'dark:text-red-400');
                    }
                }
            };

            ['province_code', 'city_code', 'district_code'].forEach((id) => {
                document.getElementById(id).addEventListener('change', () => reset('Pilih alamat pengiriman terlebih dahulu.'));
            });
            village.addEventListener('change', load);
            load();
        })();
    </script>
@endsection
