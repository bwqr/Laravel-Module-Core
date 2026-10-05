<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    protected $fillable = [
        'category_id', 'locale', 'name', 'slug', 'description', 'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
