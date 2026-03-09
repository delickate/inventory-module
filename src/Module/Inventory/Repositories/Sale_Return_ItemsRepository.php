<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Sale_Return_Items;
use Modules\Inventory\Repositories\Interfaces\Sale_Return_ItemsRepositoryInterface;

class Sale_Return_ItemsRepository implements Sale_Return_ItemsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Sale_Return_Items::with(['Sale_Returns', 'Products', ])->get();
        }

        return Sale_Return_Items::with(['Sale_Returns', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Sale_Return_Items::with(['Sale_Returns', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Sale_Return_Items::create($data);
    }

    public function update($id, array $data)
    {
        $record = Sale_Return_Items::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Sale_Return_Items::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

