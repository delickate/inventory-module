<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Inventory;
use Modules\Inventory\Repositories\Interfaces\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Inventory::with(['Warehouses', 'Products', ])->get();
        }

        return Inventory::with(['Warehouses', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Inventory::with(['Warehouses', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Inventory::create($data);
    }

    public function update($id, array $data)
    {
        $record = Inventory::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Inventory::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

