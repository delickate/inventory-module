<?php

namespace Modules\Inventory\Repositories;

use Modules\Inventory\Entities\Users;
use Modules\Inventory\Entities\UsersHasRoles;
use Modules\Inventory\Repositories\Interfaces\UsersRepositoryInterface;
use DB;

class UsersRepository implements UsersRepositoryInterface
{
    public function getAll($perPage, $search)
    {
        $result = Users::with([]);

        if (!empty($search)) 
        {
            $result = $result->where($search);
        }

        //dd($search, $result->toSql());

        $result = $result->paginate($perPage);

        return $result;
    }

    public function findById($id)
    {
        return Users::with(['roles'])->find($id);
    }

    public function create(array $data, $request)
    {
        // Start a transaction
        DB::beginTransaction();

        try {
                // Perform database operations
                $user = Users::create($data);

                if($request->role_id)
                {
                    foreach ($request->role_id as $role) 
                    {
                        $roleData = ['user_id' => $user->id, 'role_id' => $role];
                        UsersHasRoles::create($roleData);
                    }
                }
                //$user->roles()->sync($request->role_id);
                
                // Commit the transaction
                DB::commit();

                return $user;
                
            } catch (\Exception $e) 
                    {
                        // Rollback the transaction on error
                        DB::rollBack();
                        
                        // Handle the exception
                        //throw $e;

                        return $e;
                    }

        
    }

    public function update($id, array $data, $request)
    {
        // Start a transaction
        DB::beginTransaction();

        try {
            // Find the user
            $user = Users::findOrFail($id);
            
            // Update user details
            $user->update($data);
            
            // Handle roles update
            if ($request->has('role_id')) {
                // First delete all existing roles
                UsersHasRoles::where('user_id', $user->id)->delete();
                
                // Add new roles
                foreach ($request->role_id as $role) {
                    $roleData = ['user_id' => $user->id, 'role_id' => $role];
                    UsersHasRoles::create($roleData);
                }
                
                // Alternative approach using sync (more efficient)
                // $user->roles()->sync($request->role_id);
            }
            
            // Commit the transaction
            DB::commit();

            return $user;
            
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            
            // Handle the exception
            return $e;
        }
    }

    public function delete($id)
    {
        $record = Users::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

