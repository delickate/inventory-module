<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Roles;
use Modules\Inventory\Repositories\Interfaces\RolesRepositoryInterface;

class RolesRepository implements RolesRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Roles::with([])->get();
        }

        return Roles::with([])->paginate($perPage);
    }

    public function findById($id)
    {
        return Roles::with([])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Roles::create($data);
    }

    public function update($id, array $data)
    {
        $role = Roles::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $record = Roles::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

