<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Units;
use Modules\Inventory\Repositories\Interfaces\UnitsRepositoryInterface;

class UnitsRepository implements UnitsRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Units::with([])->get();
        }

        return Units::with([])->paginate($perPage);
    }

    public function findById($id)
    {
        return Units::with([])->find($id);
    }

    public function create(array $data)
    {
        return Units::create($data);
    }

    public function update($id, array $data)
    {
        $record = Units::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Units::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

