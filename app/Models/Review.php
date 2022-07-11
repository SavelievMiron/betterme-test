<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'rate'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function getCreatedAtAttribute(string $value)
    {
        return Carbon::parse($value)->toDateTimeString();
    }

    public function getUpdatedAtAttribute(string $value)
    {
        return Carbon::parse($value)->toDateTimeString();
    }
}
