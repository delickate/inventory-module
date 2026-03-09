<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Purchases;
use Modules\Inventory\Repositories\Interfaces\PurchasesRepositoryInterface;

class PurchasesRepository implements PurchasesRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Purchases::with(['Suppliers', ])->get();
        }

        return Purchases::with(['Suppliers', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Purchases::with(['Suppliers', ])->find($id);
    }

    public function create(array $data)
    {
        return Purchases::create($data);
    }

    public function update($id, array $data)
    {
        $record = Purchases::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Purchases::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

