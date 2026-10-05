<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    protected $casts = [ 'id' => 'integer' ];

    protected $fillable = [
      'name', 'description', 'slug'
    ];

    protected $hidden = [

    ];

    protected $dates = ['deleted_at'];

    public function articles()
    {
      return $this->belongsToMany('App\Modules\Core\Article', 'article_categories');
    }

    public function articleContents()
    {
      return $this->hasManyThrough('App\Modules\Core\ArticleContent', 'App\Modules\Core\ArticleCategory', 'category_id', 'article_id', 'id', 'article_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation($locale)
    {
        return $this->hasOne(CategoryTranslation::class)->where('locale', $locale);
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
