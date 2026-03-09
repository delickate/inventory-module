<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Customers;
use Modules\Inventory\Repositories\Interfaces\CustomersRepositoryInterface;

class CustomersRepository implements CustomersRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Customers::with([])->get();
        }

        return Customers::with([])->paginate($perPage);
    }

    public function findById($id)
    {
        return Customers::with([])->find($id);
    }

    public function create(array $data)
    {
        return Customers::create($data);
    }

    public function update($id, array $data)
    {
        $record = Customers::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Customers::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

