<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

use Modules\Inventory\Entities\AutoVouchingInventory;

class AutoVouchingInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $entries = AutoVouchingInventory::all();
    
        return view('inventory::auto_vouching/index', compact('entries'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('inventory::auto_vouching/create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('inventory::auto_vouching/show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data = [];
        $data["title"] = "Auto vouching";
        $data['entry'] = AutoVouchingInventory::findOrFail($id);

        
        return view('inventory::auto_vouching/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
       $request->validate([
            'module_name' => 'required|string',
            'action_type' => 'required|string',
            'debit_account_id' => 'required|integer',
            'credit_account_id' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        $entry = AutoVouchingInventory::findOrFail($id);
        $entry->update($request->only(['module_name', 'action_type', 'debit_account_id', 'credit_account_id', 'description']));

        return redirect()->route('integration.listing')->with('success', 'Integration updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
