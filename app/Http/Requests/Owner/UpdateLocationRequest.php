<?php

namespace App\Http\Requests\Owner;

use Illuminate\Contracts\Validation\Validator;

class UpdateLocationRequest extends StoreLocationRequest
{
    // Editing starts from the gallery the listing already has, so uploads are
    // optional here. What matters is the size of the gallery the owner ends up
    // with once removals and new uploads are applied.
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'images'        => ['nullable', 'array', 'max:'.self::MAX_IMAGES],
            'image_order'   => ['nullable', 'array', 'max:'.self::MAX_IMAGES],
            'image_order.*' => ['string'],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $total = count($this->keptImages()) + count($this->file('images', []));
            $min   = $this->minimumImages();

            if ($total < $min) {
                $validator->errors()->add('images', $min === 1
                    ? 'A listing needs at least one image.'
                    : 'Please keep or upload at least '.$min.' images of the location.');
            }

            if ($total > self::MAX_IMAGES) {
                $validator->errors()->add('images', 'You can have a maximum of '.self::MAX_IMAGES.' images.');
            }
        });
    }

    /**
     * The images already on the listing that the owner chose to keep, in the
     * order they arranged them. Unknown paths are ignored so the form cannot
     * be used to point the gallery at arbitrary files.
     *
     * @return array<int, string>
     */
    public function keptImages(): array
    {
        $gallery = $this->currentGallery();
        $order   = $this->input('image_order');

        if (! is_array($order)) {
            return $gallery;   // No ordering posted — nothing was removed.
        }

        return array_values(array_unique(array_filter(
            $order,
            fn($token) => is_string($token) && in_array($token, $gallery, true)
        )));
    }

    /** @return array<int, string> */
    public function currentGallery(): array
    {
        $location = $this->route('location');

        return $location ? $location->gallery() : [];
    }

    /**
     * Listings created before galleries existed can hold fewer than MIN_IMAGES
     * photos; their owners only have to keep what they already had.
     */
    private function minimumImages(): int
    {
        return min(self::MIN_IMAGES, max(count($this->currentGallery()), 1));
    }
}
