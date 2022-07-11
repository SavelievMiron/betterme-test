<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Book::truncate();

        Book::factory()->count(10)->create()->each(function ($book) {
            $book->author()->associate(User::inRandomOrder()->limit(1)->value('id'));

            $book->save();
        });
    }
}
