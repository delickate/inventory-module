<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Sale_Returns;
use Modules\Inventory\Repositories\Interfaces\Sale_ReturnsRepositoryInterface;

class Sale_ReturnsRepository implements Sale_ReturnsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Sale_Returns::with(['Sales', 'Customers', ])->get();
        }

        return Sale_Returns::with(['Sales', 'Customers', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Sale_Returns::with(['Sales', 'Customers', ])->find($id);
    }

    public function create(array $data)
    {
        return Sale_Returns::create($data);
    }

    public function update($id, array $data)
    {
        $record = Sale_Returns::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Sale_Returns::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

