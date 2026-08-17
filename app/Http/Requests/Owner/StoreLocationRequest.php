<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public const MIN_IMAGES = 3;
    public const MAX_IMAGES = 7;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', 'max:100'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'description'    => ['required', 'string'],
            'address'        => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:100'],
            'images'         => ['required', 'array', 'min:'.self::MIN_IMAGES, 'max:'.self::MAX_IMAGES],
            'images.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Please upload at least '.self::MIN_IMAGES.' images of the location.',
            'images.min'      => 'Please upload at least '.self::MIN_IMAGES.' images of the location.',
            'images.max'      => 'You can upload a maximum of '.self::MAX_IMAGES.' images.',
            'images.*.image'  => 'Each upload must be an image file.',
            'images.*.mimes'  => 'Images must be PNG, JPG, JPEG or WEBP files.',
            'images.*.max'    => 'Each image must be 4MB or smaller.',
        ];
    }
}
