<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    protected $casts = [ 'id' => 'integer' ];

    protected $guarded = [];

    public function menus()
    {
      return $this->belongsToMany('App\Modules\Core\Menu', 'menu_roles');
    }

    public function users()
    {
      return $this->belongsToMany('App\Modules\Core\User', 'user_datas', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Modules\Core\Permission', 'role_permissions');
    }
}
