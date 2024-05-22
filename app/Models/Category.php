<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    protected static function booted()
    {
        static::deleting(function (Category $category) {
            $category->books()->each(function ($book) {
                $book->delete();
            });
        });

        static::restoring(function (Category $category) {
            $category->books()->withTrashed()->each(function ($book) {
                $book->restore();
            });
        });
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
