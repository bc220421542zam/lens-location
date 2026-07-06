<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Console\Command;

class BackfillCategoryIds extends Command
{
    protected $signature = 'listings:backfill-categories';
    protected $description = 'Link existing listings to their matching category_id';

    public function handle(): void
    {
        $listings = Location::whereNull('category_id')->get();

        $this->info("Found {$listings->count()} listings with no category_id.");

        foreach ($listings as $listing) {
            $categoryName = strtolower(trim($listing->category));

            $category = Category::whereRaw('LOWER(name) = ?', [$categoryName])->first();

            if ($category) {
                $listing->category_id = $category->id;
                $listing->save();
                $this->info("Listing {$listing->id} ('{$listing->category}') -> category_id {$category->id}");
            } else {
                $this->warn("Listing {$listing->id} ('{$listing->category}') -> NO MATCH FOUND");
            }
        }

        $this->info('Done.');
    }
}