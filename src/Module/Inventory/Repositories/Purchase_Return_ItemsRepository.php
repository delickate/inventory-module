<?php

namespace App\Repositories;

use App\Models\Purchase_Return_Items;
use App\Repositories\Interfaces\Purchase_Return_ItemsRepositoryInterface;

class Purchase_Return_ItemsRepository implements Purchase_Return_ItemsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Purchase_Return_Items::with(['Purchase_Returns', 'Products', ])->get();
        }

        return Purchase_Return_Items::with(['Purchase_Returns', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Purchase_Return_Items::with(['Purchase_Returns', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Purchase_Return_Items::create($data);
    }

    public function update($id, array $data)
    {
        $record = Purchase_Return_Items::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Purchase_Return_Items::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

