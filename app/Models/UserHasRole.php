<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasRole extends Model
{
    use HasFactory;
    protected $guarded  = [];

    public function role(){
        return $this->HasMany(Role::class,'id','role_id');
    }

}
