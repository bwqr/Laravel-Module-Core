<?php

namespace App\Modules\Core;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    use SoftDeletes;

      /**
       * The attributes that should be mutated to dates.
       *
       * @var array
       */
    protected $dates = ['deleted_at'];

    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * Get the identifier that will be stored in the subject claim of the JWT.
    *
    * @return mixed
    */
     public function getJWTIdentifier()
     {
         return $this->getKey();
     }
     /**
      * Return a key value array, containing any custom claims to be added to the JWT.
      *
      * @return array
      */
     public function getJWTCustomClaims()
     {
         return [];
     }

     protected $casts = [ 'id' => 'integer' ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function menus()
    {
      return $this->belongsToMany('App');
    }

    public function userData()
    {
      return $this->hasOne('App\Modules\Core\UserData', 'user_id', 'user_id');
    }

    public function articles()
    {
      return $this->belongsToMany('App\Modules\Core\Article', 'article_permissions', 'user_id', 'article_id');
    }

    public function trashedArticles()
    {
      return $this->articles()->onlyTrashed();
    }

    public function images()
    {
        return $this->hasMany('App\\Modules\\Image\Image', 'owner', 'user_id');
    }

    //Deprecated
    public function roles()
    {
      return $this->belongsToMany('App\Modules\Core\Role', 'user_datas', 'user_id', 'role_id');
    }

    public function rolesByRoleId($role_id)
    {
      return $this->belongsToMany('App\Modules\Core\Role', 'user_datas', 'user_id', 'role_id')->wherePivot('role_id', $role_id);
    }

    public function permission($permission)
    {
        return $this->roles[0]->permissions()->where('slug', $permission);
    }

    public function menusByRole()
    {
      $roles = $this->roles;

      $menus = []; $i = 0;

      foreach ($roles as $value) {

        foreach ($value->menus as $val2) {

          $menus[$i] = $val2;

          $i++;
        }
      }

      return $menus;
    }

    public function getRoleAttribute()
    {
        return $this->roles[0];
    }
}
