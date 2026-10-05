<?php

namespace App\Modules\Core;

use App\Modules\Core\Traits\NPerGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ModelTraits\ArticleTrait;

class Article extends Model
{
    use SoftDeletes;
    use NPerGroup;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = ['id' => 'integer', 'views' => 'integer'];

    protected $fillable = [
        'slug', 'author_id', 'public_author_id', 'image', 'views'
    ];

    protected $hidden = [

    ];

    public function languages()
    {
        return $this->belongsToMany('App\Modules\Core\Language', 'article_contents', 'article_id', 'language_id');
    }

    public function availableLanguages($published = 1)
    {
        return $this->belongsToMany('App\Modules\Core\Language', 'article_contents', 'article_id', 'language_id')->wherePivot('published', $published);
    }

    public function categories()
    {
        return $this->belongsToMany('App\Modules\Core\Category', 'article_categories');
    }

    public function article_categories()
    {
        return $this->hasMany('App\Modules\Core\ArticleCategory');
    }

    public function users()
    {
        return $this->belongsToMany('App\Modules\Core\User', 'article_permissions', 'article_id', 'user_id');
    }

    public function permissions()
    {
        return $this->hasMany('App\Modules\Core\ArticlePermission');
    }

    public function contents()
    {
        return $this->hasMany('App\Modules\Core\ArticleContent');
    }

    public function content()
    {
        return $this->hasOne('App\Modules\Core\ArticleContent');
    }

    public function author()
    {
        return $this->belongsTo('App\Modules\Core\User', 'author_id', 'user_id');
    }

    public function publicAuthor()
    {
        return $this->belongsTo(Author::class, 'public_author_id');
    }

    public function slugHistories()
    {
        return $this->hasMany(ArticleSlugHistory::class);
    }

    public function olds()
    {
        return $this->hasMany('App\Modules\Core\ArticleArchive', 'article_id', 'id');
    }

    public function contentByLanguage($language)
    {
        return $this->hasOne('App\Modules\Core\ArticleContent')->where('language_id', $language);
    }

    public function trashed_categories()
    {
        return $this->hasMany('App\Modules\Core\ArticleCategory')->onlyTrashed();
    }

    public function trashed_contents()
    {
        return $this->hasMany('App\Modules\Core\ArticleContent', 'article_id', 'id')->onlyTrashed();
    }

    public function room()
    {
        return $this->hasOne('App\Modules\Core\ArticleRoom');
    }

    public function createMessage($message)
    {
        $new_message = $this->room->messages()->create([
            'room_id' => $this->room->id,
            'user_id' => auth()->user()->user_id,
            'message' => $message
        ]);

        return $new_message;
    }

    public function scopeWhereHasContent($query, $language_id)
    {
        return $query->whereHas('content', function ($query_content) use ($language_id) {
            $query_content->where('language_id', $language_id);
        });
    }

    public function scopeWithContent($query, $language_id)
    {
        return $query->with(['content' => function ($query_content) use ($language_id) {
            $query_content->where('language_id', $language_id);
        }]);
    }

    public function scopeWhereHasPublishedContent($query, $language_id, $published = 1)
    {
        return $query->whereHas('content', function ($query_content) use ($language_id, $published) {
            $query_content->where('language_id', $language_id)->published($published);
        });
    }

    public function scopeWithPublishedContent($query, $language_id, $published = 1)
    {
        return $query->with(['content' => function ($query_content) use ($language_id, $published) {
            $query_content->where('language_id', $language_id)->published($published);
        }]);
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeWhereId($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeWithRoomAndMessages($query)
    {
        return $query->with(['room' => function ($q) {
            $q->withMessagesAndUser();
        }]);
    }

    public function getCategoryAttribute()
    {
        $category = count($this->categories) > 0 ? $this->categories[0] : new Category([
            'name' => '',
            'slug' => ''
        ]);

        return $category;
    }
}
