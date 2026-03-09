<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersHasRoles extends Model
{
    use HasFactory;

    protected $table = 'users_has_roles';

    protected $fillable = [
        'user_id',
        'role_id'
    ];
}
