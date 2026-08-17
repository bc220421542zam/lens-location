<?php

namespace App\Http\Requests\Owner;

class UpdateLocationRequest extends StoreLocationRequest
{
    // Editing keeps the gallery it already has, so uploads are optional here.
    // When new images are supplied they replace the whole gallery and must
    // therefore still satisfy the 3–7 range.
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'images' => ['nullable', 'array', 'min:'.self::MIN_IMAGES, 'max:'.self::MAX_IMAGES],
            'image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }
}
