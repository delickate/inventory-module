<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Warehouses;
use Modules\Inventory\Repositories\Interfaces\WarehousesRepositoryInterface;

class WarehousesRepository implements WarehousesRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Warehouses::with([])->get();
        }

        return Warehouses::with([])->paginate($perPage);
    }

    public function findById($id)
    {
        return Warehouses::with([])->find($id);
    }

    public function create(array $data)
    {
        return Warehouses::create($data);
    }

    public function update($id, array $data)
    {
        $record = Warehouses::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Warehouses::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

