<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Suppliers;
use Modules\Inventory\Repositories\Interfaces\SuppliersRepositoryInterface;

class SuppliersRepository implements SuppliersRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Suppliers::with([])->get();
        }

        return Suppliers::with([])->paginate($perPage);
    }

    public function findById($id)
    {
        return Suppliers::with([])->find($id);
    }

    public function create(array $data)
    {
        return Suppliers::create($data);
    }

    public function update($id, array $data)
    {
        $record = Suppliers::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Suppliers::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

