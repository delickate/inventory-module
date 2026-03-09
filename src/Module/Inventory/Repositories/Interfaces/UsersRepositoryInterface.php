<?php

namespace Modules\Inventory\Repositories\Interfaces;

interface UsersRepositoryInterface
{
    public function getAll($perPage, $search);
    public function findById($id);
    public function create(array $data, $request);
    public function update($id, array $data, $request);
    public function delete($id);
}
