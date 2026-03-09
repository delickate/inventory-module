<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Purchase_Returns;
use Modules\Inventory\Repositories\Interfaces\Purchase_ReturnsRepositoryInterface;

class Purchase_ReturnsRepository implements Purchase_ReturnsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Purchase_Returns::with(['Purchases', 'Products', ])->get();
        }

        return Purchase_Returns::with(['Purchases', 'Products', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Purchase_Returns::with(['Purchases', 'Products', ])->find($id);
    }

    public function create(array $data)
    {
        return Purchase_Returns::create($data);
    }

    public function update($id, array $data)
    {
        $record = Purchase_Returns::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Purchase_Returns::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

