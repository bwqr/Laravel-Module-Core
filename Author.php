<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'erp_person_uuid', 'profile_image', 'linkedin_url', 'social_links', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'social_links' => 'array',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function translations()
    {
        return $this->hasMany(AuthorTranslation::class);
    }

    public function translation($locale)
    {
        return $this->hasOne(AuthorTranslation::class)->where('locale', $locale);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'public_author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
