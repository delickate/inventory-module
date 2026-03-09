<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Categories;
use Modules\Inventory\Repositories\Interfaces\CategoriesRepositoryInterface;

class CategoriesRepository implements CategoriesRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        if (!empty($search)) {
            return Categories::with([])->get();
        }

        return Categories::with([])->paginate($perPage);
    }

    public function findById($id)
    {
        return Categories::with([])->find($id);
    }

    public function create(array $data)
    {
        return Categories::create($data);
    }

    public function update($id, array $data)
    {
        $record = Categories::find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete($id)
    {
        $record = Categories::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

