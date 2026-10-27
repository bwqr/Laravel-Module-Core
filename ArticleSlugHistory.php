<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;

class ArticleSlugHistory extends Model
{
    protected $fillable = ['article_id', 'locale', 'slug'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
