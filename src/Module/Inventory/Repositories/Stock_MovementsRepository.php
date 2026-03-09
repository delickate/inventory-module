<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Stock_Movements;
use Modules\Inventory\Repositories\Interfaces\Stock_MovementsRepositoryInterface;

class Stock_MovementsRepository implements Stock_MovementsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Stock_Movements::with(['Warehouses', 'Products', ])->get();
        }

        return Stock_Movements::with(['Warehouses', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Stock_Movements::with(['Warehouses', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Stock_Movements::create($data);
    }

    public function update($id, array $data)
    {
        $record = Stock_Movements::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Stock_Movements::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

