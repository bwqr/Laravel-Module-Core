<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;

class AuthorTranslation extends Model
{
    protected $fillable = [
        'author_id', 'locale', 'slug', 'name', 'professional_title', 'biography', 'expertise',
    ];

    protected $casts = [
        'expertise' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
