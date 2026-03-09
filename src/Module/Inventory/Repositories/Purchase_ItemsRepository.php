<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Purchase_Items;
use Modules\Inventory\Repositories\Interfaces\Purchase_ItemsRepositoryInterface;

class Purchase_ItemsRepository implements Purchase_ItemsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Purchase_Items::with(['Purchases', 'Products', ])->get();
        }

        return Purchase_Items::with(['Purchases', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Purchase_Items::with(['Purchases', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Purchase_Items::create($data);
    }

    public function update($id, array $data)
    {
        $record = Purchase_Items::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Purchase_Items::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

