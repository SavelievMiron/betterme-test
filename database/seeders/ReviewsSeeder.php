<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Review::truncate();

        Review::factory()->count(10)->create()->each(function ($review) {
            $review->book()->associate(Book::inRandomOrder()->limit(1)->value('id'));
            $review->author()->associate(User::inRandomOrder()->limit(1)->value('id'));

            $review->save();
        });
    }
}
