<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    use HasFactory;
    protected $guarded  = [];

    public function role(){
        return $this->HasMany(Role::class,'id','role_id');
    }

    public function permission(){
        return $this->HasMany(Permission::class,'id','permission_id');
    }

}
