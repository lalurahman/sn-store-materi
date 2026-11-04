<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravolt\Indonesia\Models\Village;

class RajaOngkirService
{
    /**
     * Get shipping options from the store origin (default: Makassar) to a village.
     *
     * @return list<array{courier: string, courier_name: string, service: string, description: string, cost: int, etd: string, key: string}>
     */
    public function getShippingOptions(Village $village, int $weightInGrams): array
    {
        try {
            $destinationId = $this->findDestinationId($village);

            $response = $this->request()->asForm()->post('/calculate/domestic-cost', [
                'origin' => config('services.rajaongkir.origin_id'),
                'destination' => $destinationId,
                'weight' => max($weightInGrams, 1),
                'courier' => implode(':', config('services.rajaongkir.couriers')),
                'price' => 'lowest',
            ]);
        } catch (ConnectionException $exception) {
            throw new RajaOngkirException('Tidak dapat terhubung ke RajaOngkir. Silakan coba kembali.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new RajaOngkirException('RajaOngkir gagal menghitung ongkos kirim (HTTP '.$response->status().').');
        }

        $options = collect($response->json('data', []))
            ->filter(fn ($option): bool => is_array($option) && isset($option['code'], $option['service'], $option['cost']))
            ->map(fn (array $option): array => [
                'courier' => (string) $option['code'],
                'courier_name' => (string) ($option['name'] ?? strtoupper($option['code'])),
                'service' => (string) $option['service'],
                'description' => (string) ($option['description'] ?? ''),
                'cost' => (int) $option['cost'],
                'etd' => trim((string) ($option['etd'] ?? '')),
                'key' => $option['code'].'|'.$option['service'],
            ])
            ->sortBy('cost')
            ->values()
            ->all();

        if ($options === []) {
            throw new RajaOngkirException('Tidak ada layanan pengiriman yang tersedia untuk alamat ini.');
        }

        return $options;
    }

    /**
     * Resolve one option server-side so the client cannot choose its own price.
     *
     * @return array{courier: string, courier_name: string, service: string, description: string, cost: int, etd: string, key: string}
     */
    public function findShippingOption(Village $village, int $weightInGrams, string $optionKey): array
    {
        foreach ($this->getShippingOptions($village, $weightInGrams) as $option) {
            if ($option['key'] === $optionKey) {
                return $option;
            }
        }

        throw new RajaOngkirException('Layanan pengiriman yang dipilih tidak tersedia. Silakan pilih ulang.');
    }

    public function weightForQuantity(int $quantity): int
    {
        return max($quantity, 1) * (int) config('services.rajaongkir.item_weight', 500);
    }

    private function findDestinationId(Village $village): int
    {
        $village->loadMissing('district.city');
        $districtName = $this->normalize($village->district->name);
        $cityName = $this->normalize($village->district->city->name);

        $candidates = [];

        foreach ([$village->name, $village->district->name] as $keyword) {
            $response = $this->request()->get('/destination/domestic-destination', [
                'search' => $keyword,
                'limit' => 50,
                'offset' => 0,
            ]);

            if (! $response->successful()) {
                throw new RajaOngkirException('RajaOngkir gagal mencari tujuan pengiriman (HTTP '.$response->status().').');
            }

            $candidates = collect($response->json('data', []))
                ->filter(fn ($row): bool => is_array($row) && isset($row['id']) && $this->normalize((string) ($row['city_name'] ?? '')) === $cityName)
                ->values()
                ->all();

            $sameDistrict = array_values(array_filter(
                $candidates,
                fn (array $row): bool => $this->normalize((string) ($row['district_name'] ?? '')) === $districtName,
            ));

            foreach ($sameDistrict as $row) {
                if ($this->normalize((string) ($row['subdistrict_name'] ?? '')) === $this->normalize($village->name)) {
                    return (int) $row['id'];
                }
            }

            if ($sameDistrict !== []) {
                return (int) $sameDistrict[0]['id'];
            }
        }

        if ($candidates !== []) {
            return (int) $candidates[0]['id'];
        }

        throw new RajaOngkirException('Tujuan pengiriman tidak ditemukan di RajaOngkir. Periksa kembali alamat pengiriman.');
    }

    private function normalize(string $name): string
    {
        return (string) Str::of($name)
            ->upper()
            ->replaceMatches('/^(KOTA ADM\.?|KOTA|KABUPATEN|KAB\.?)\s+/', '')
            ->replaceMatches('/[^A-Z0-9]+/', ' ')
            ->trim();
    }

    private function request(): PendingRequest
    {
        $key = config('services.rajaongkir.key');

        if (! is_string($key) || $key === '') {
            throw new RajaOngkirException('Konfigurasi RajaOngkir belum lengkap. Atur RAJAONGKIR_API_KEY terlebih dahulu.');
        }

        return Http::acceptJson()
            ->baseUrl((string) config('services.rajaongkir.base_url'))
            ->withHeaders(['key' => $key])
            ->connectTimeout(3)
            ->timeout(10);
    }
}
