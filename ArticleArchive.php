<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleArchive extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
  protected $dates = ['deleted_at', 'published_at'];

    protected $casts = [
        'id' => 'integer',
        'article_id' => 'integer',
        'language_id' => 'integer',
        'keywords' => 'array',
        'published' => 'integer',
        'version' => 'integer',
    ];

    protected $fillable = [
    'article_id', 'title', 'language_id', 'slug', 'body', 'sub_title', 'excerpt',
    'meta_title', 'meta_description', 'keywords', 'published', 'published_at', 'situation', 'version'
  ];

  protected $hidden = [

  ];

  public function sa() {
      $a = function () { $contents = ArticleContent::all(); foreach ($contents as $content) { $content->keywords = explode(',', $content->keywords); $content->save();}};
  }
}
