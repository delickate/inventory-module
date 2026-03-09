<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Products;
use Modules\Inventory\Repositories\Interfaces\ProductsRepositoryInterface;

class ProductsRepository implements ProductsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Products::with(['Categories', 'Units', ])->get();
        }

        return Products::with(['Categories', 'Units', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Products::with(['Categories', 'Units', ])->find($id);
    }

    public function create(array $data)
    {
        return Products::create($data);
    }

    public function update($id, array $data)
    {
        $record = Products::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Products::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

