<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Sales;
use Modules\Inventory\Repositories\Interfaces\SalesRepositoryInterface;

class SalesRepository implements SalesRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Sales::with(['Customers', ])->get();
        }

        return Sales::with(['Customers', ])->paginate($perPage);
    }

    public function findById($id)
    {
        return Sales::with(['Customers', ])->find($id);
    }

    public function create(array $data)
    {
        return Sales::create($data);
    }

    public function update($id, array $data)
    {
        $record = Sales::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Sales::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

