<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'tag_name',
        'class_name',
        'is_mandatory',
        'is_active',
        'sortorder'
    ];

    /**
     * Get the parent module
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    /**
     * Get the child modules
     */
    public function children(): HasMany
    {
        return $this->hasMany(Module::class, 'parent_id')->orderBy('sortorder');
    }


    public function rolePermissions()
    {
        return $this->belongsToMany(
            Role::class,
            'roles_has_permissions',
            'module_id',
            'role_id'
        )->using(RolePermission::class)
         ->withPivot([
            'can_view',
            'can_add',
            'can_edit',
            'can_delete',
            'can_approve',
            'can_export',
            'can_print'
        ]);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_has_permissions')
                    ->using(RolePermission::class)
                    ->withPivot([
                        'can_view',
                        'can_add',
                        'can_edit',
                        'can_delete',
                        'can_approve',
                        'can_export',
                        'can_print'
                    ]);
    }

}
