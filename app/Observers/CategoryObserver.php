<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function deleting(Category $category): void
    {
        $category->books()->each(function ($book) {
            $book->delete();
        });
    }
    
    public function restoring(Category $category): void
    {
        $category->books()->withTrashed()->each(function ($book) {
            $book->restore();
        });
    }
}
