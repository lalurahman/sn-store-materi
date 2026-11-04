<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\ShippingCostRequest;
use App\Models\Category;
use App\Services\CartService;
use App\Services\MidtransPaymentException;
use App\Services\MidtransPaymentService;
use App\Services\RajaOngkirException;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $cartService): View|RedirectResponse
    {
        $cartSummary = $cartService->getCartSummary($request->user());

        if ($cartSummary['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang kosong. Tambahkan produk sebelum checkout.');
        }

        $provinceCode = $request->old('province_code');
        $cityCode = $request->old('city_code');
        $districtCode = $request->old('district_code');
        $cities = is_string($provinceCode)
            ? City::query()->where('province_code', $provinceCode)->orderBy('name')->get(['code', 'name'])
            : collect();
        $selectedCity = is_string($cityCode) ? $cities->firstWhere('code', $cityCode) : null;
        $districts = $selectedCity instanceof City
            ? $selectedCity->districts()->orderBy('name')->get(['code', 'name'])
            : collect();
        $selectedDistrict = is_string($districtCode) ? $districts->firstWhere('code', $districtCode) : null;
        $villages = $selectedDistrict instanceof District
            ? $selectedDistrict->villages()->orderBy('name')->get(['code', 'name'])
            : collect();

        return view('pages.public.checkout.index', [
            ...$cartSummary,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'cities' => $cities,
            'districts' => $districts,
            'provinces' => Province::query()->orderBy('name')->get(['code', 'name']),
            'searchQuery' => '',
            'user' => $request->user(),
            'villages' => $villages,
        ]);
    }

    public function cities(string $provinceCode): JsonResponse
    {
        $province = Province::query()->where('code', $provinceCode)->firstOrFail();

        return response()->json($province->cities()
            ->orderBy('name')
            ->get(['code', 'name'])
            ->map(fn (City $city): array => ['code' => $city->code, 'name' => $city->name])
            ->values());
    }

    public function districts(string $cityCode): JsonResponse
    {
        $city = City::query()->where('code', $cityCode)->firstOrFail();

        return response()->json($city->districts()
            ->orderBy('name')
            ->get(['code', 'name'])
            ->map(fn (District $district): array => ['code' => $district->code, 'name' => $district->name])
            ->values());
    }

    public function villages(string $districtCode): JsonResponse
    {
        $district = District::query()->where('code', $districtCode)->firstOrFail();

        return response()->json($district->villages()
            ->orderBy('name')
            ->get(['code', 'name'])
            ->map(fn (Village $village): array => ['code' => $village->code, 'name' => $village->name])
            ->values());
    }

    public function shippingCosts(
        ShippingCostRequest $request,
        CartService $cartService,
        RajaOngkirService $rajaOngkirService,
    ): JsonResponse {
        $village = Village::query()->where('code', $request->validated('village_code'))->firstOrFail();
        $itemCount = $cartService->getCartSummary($request->user())['itemCount'];

        try {
            $options = $rajaOngkirService->getShippingOptions($village, $rajaOngkirService->weightForQuantity($itemCount));
        } catch (RajaOngkirException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['options' => $options]);
    }

    public function store(
        CheckoutRequest $request,
        CartService $cartService,
        MidtransPaymentService $midtransPaymentService,
        RajaOngkirService $rajaOngkirService,
    ): RedirectResponse {
        try {
            $order = $cartService->checkout($request->user(), $request->validated(), $midtransPaymentService, $rajaOngkirService);
        } catch (RajaOngkirException $exception) {
            return back()
                ->withInput()
                ->withErrors(['shipping_option' => $exception->getMessage()]);
        } catch (MidtransPaymentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['payment' => $exception->getMessage()]);
        }

        return redirect()->route('orders.show', $order->order_number);
    }
}
