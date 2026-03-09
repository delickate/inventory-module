<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Sale_Items;
use Modules\Inventory\Repositories\Interfaces\Sale_ItemsRepositoryInterface;

class Sale_ItemsRepository implements Sale_ItemsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Sale_Items::with(['Sales', 'Products', ])->get();
        }

        return Sale_Items::with(['Sales', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Sale_Items::with(['Sales', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Sale_Items::create($data);
    }

    public function update($id, array $data)
    {
        $record = Sale_Items::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Sale_Items::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

