<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'province_code' => ['required', 'string', Rule::exists((new Province)->getTable(), 'code')],
            'city_code' => ['required', 'string', Rule::exists((new City)->getTable(), 'code')],
            'district_code' => ['required', 'string', Rule::exists((new District)->getTable(), 'code')],
            'village_code' => ['required', 'string', Rule::exists((new Village)->getTable(), 'code')],
            'postal_code' => ['required', 'regex:/^\d{5}$/'],
            'shipping_option' => ['required', 'string', 'regex:/^[a-z0-9]+\|[^|]{1,50}$/i'],
        ];
    }

    /**
     * Validate that each selected region belongs to the selected parent.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            foreach (['province_code', 'city_code', 'district_code', 'village_code'] as $field) {
                if ($validator->errors()->has($field)) {
                    return;
                }
            }

            $city = City::query()
                ->where('code', $this->input('city_code'))
                ->where('province_code', $this->input('province_code'))
                ->first();

            if (! $city instanceof City) {
                $validator->errors()->add('city_code', 'Kabupaten/kota tidak sesuai dengan provinsi yang dipilih.');

                return;
            }

            $district = District::query()
                ->where('code', $this->input('district_code'))
                ->where('city_code', $city->code)
                ->first();

            if (! $district instanceof District) {
                $validator->errors()->add('district_code', 'Kecamatan tidak sesuai dengan kabupaten/kota yang dipilih.');

                return;
            }

            if (! Village::query()
                ->where('code', $this->input('village_code'))
                ->where('district_code', $district->code)
                ->exists()) {
                $validator->errors()->add('village_code', 'Desa/kelurahan tidak sesuai dengan kecamatan yang dipilih.');
            }
        }];
    }
}
