<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Stock_MovementsRepositoryInterface;
use Modules\Inventory\Entities\Stock_Movements;
use Modules\Inventory\Entities\Warehouses;
use Modules\Inventory\Entities\Products;
use Modules\Inventory\Entities\Inventory;


use DB;
use DataTables;

class Stock_MovementsController extends Controller
{
    protected $stock_movementsRepository;

    public function __construct(Stock_MovementsRepositoryInterface $stock_movementsRepository)
    {
        $this->stock_movementsRepository = $stock_movementsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $stock_movements = Stock_Movements::with(['Warehouses', 'Products', ])->get();
           
        //}else{
          //      $stock_movements = Stock_Movements::with(['Warehouses', 'Products', ])->paginate($perPage);
          //   }

		//$data['stock_movements'] = $stock_movements; 

        $data['stock_movements'] = $this->stock_movementsRepository->getAll($perPage, $keyword);

        return view('inventory::stock_movements/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['warehouses'] = Warehouses::orderBy('name')->pluck('name', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::stock_movements/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_quantity' => 'required',
'form_type' => 'required',
'form_reason' => 'required',
'form_reference_id' => 'required',
'form_warehouse_id' => 'required',
'form_product_id' => 'required',

                        ]);

     $qty           = $request->post('form_quantity');
     $warehouseId   = $request->post('form_warehouse_id');
     $productId     = $request->post('form_product_id');
     $userId        = 1;
     $reason        = $request->post('form_reason');
     //$type          = $request->post('form_type');
     $reference_id  = $request->post('form_reference_id');

     try {
            DB::transaction(function () use ($productId, $warehouseId, $qty, $userId, $reason, $reference_id) 
            {
                // Step 1: Add or update inventory
                $inventory = Inventory::where('product_id', $productId)
                                      ->where('warehouse_id', $warehouseId)
                                      ->first();

                if ($inventory) 
                {
                    // If stock already exists, increase quantity
                    $inventory->quantity += $qty;
                    $inventory->save();
                } else {
                            // If no stock exists, create a new inventory record
                            Inventory::create([
                                'product_id'   => $productId,
                                'warehouse_id' => $warehouseId,
                                'quantity'     => $qty,
                            ]);
                        }

                // Step 2: Log the stock movement
                Stock_Movements::create([
                    'product_id'    => $productId,
                    'warehouse_id'  => $warehouseId,
                    'type'          => 'adjustment',  //'inward', 'outward', 'adjustment'
                    'quantity'      => $qty,
                    'reason'        => $reason,
                    'reference_id'  => $reference_id,
                    'created_by'    => $userId,
                ]);
            });

            return redirect()->route('stock_movements.listing')->with('success_message', 'Record saved successfully.');

        } catch (\Exception $e) {
            return redirect()->route('stock_movements.listing')->with('error_message', $e->getMessage());
        }

    //  $stock_movements = new Stock_Movements;
    //  $stock_movements->quantity = $request->post('form_quantity');
    // $stock_movements->type = $request->post('form_type');
    // $stock_movements->reason = $request->post('form_reason');
    // $stock_movements->reference_id = $request->post('form_reference_id');
    // $stock_movements->warehouse_id = $request->post('form_warehouse_id');
    // $stock_movements->product_id = $request->post('form_product_id');

     

    //  $data = [
    //         'quantity'        => $request->post('form_quantity'),'type'        => $request->post('form_type'),'reason'        => $request->post('form_reason'),'reference_id'        => $request->post('form_reference_id'),'warehouse_id'        => $request->post('form_warehouse_id'),'product_id'        => $request->post('form_product_id'),
    //     ];

    //  //if($stock_movements->save())
    //  if ($this->stock_movementsRepository->create($data))
    //  {
    //     return redirect()->route('stock_movements.listing')->with('success_message', 'Password has been saved successfully.');
    //  }else{
    //          return redirect()->route('stock_movements.listing')->with('error_message', 'Error while saving record.');
    //          //App::abort(500, 'Error');
    //      }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['stock_movements']=Stock_Movements::with(['Warehouses', 'Products', ])->find($id);
     $data['stock_movements'] = $this->stock_movementsRepository->findById($id);

     return view('inventory::stock_movements/show', $data);
  }

  public function edit($id)
  {

    die('not allowed');
     $data = array();
	
     $data['warehouses'] = Warehouses::orderBy('name')->pluck('name', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
		

     //$data['stock_movements']= Stock_Movements::find($id);

     $data['stock_movements'] = $this->stock_movementsRepository->findById($id);

     return view('inventory::stock_movements/edit', $data);

  }

  public function update(Request $request,$id)
  {
    die('not allowed');

    $data = array();

    $request->validate(['form_quantity' => 'required',
'form_type' => 'required',
'form_reason' => 'required',
'form_reference_id' => 'required',
'form_warehouse_id' => 'required',
'form_product_id' => 'required',
]);


     $qty           = $request->post('form_quantity');
     $warehouseId   = $request->post('form_warehouse_id');
     $productId     = $request->post('form_product_id');
     $userId        = 1;
     $reason        = $request->post('form_reason');
     //$type          = $request->post('form_type');
     $reference_id  = $request->post('form_reference_id');

     try {
            DB::transaction(function () use ($productId, $warehouseId, $qty, $userId, $reason, $reference_id) 
            {
                // Step 1: Add or update inventory
                $inventory = Inventory::where('product_id', $productId)
                                      ->where('warehouse_id', $warehouseId)
                                      ->first();

                if ($inventory) 
                {
                    // If stock already exists, increase quantity
                    $inventory->quantity += $qty;
                    $inventory->save();
                } else {
                            // If no stock exists, create a new inventory record
                            Inventory::create([
                                'product_id'   => $productId,
                                'warehouse_id' => $warehouseId,
                                'quantity'     => $qty,
                            ]);
                        }

                // Step 2: Log the stock movement
                Stock_Movements::create([
                    'product_id'    => $productId,
                    'warehouse_id'  => $warehouseId,
                    'type'          => 'adjustment',
                    'quantity'      => $qty,
                    'reason'        => $reason,
                    'reference_id'  => $reference_id,
                    'created_by'    => $userId,
                ]);
            });

            return redirect()->route('stock_movements.listing')->with('success_message', 'Record saved successfully.');

        } catch (\Exception $e) {
            return redirect()->route('stock_movements.listing')->with('error_message', $e->getMessage());
        }


//     $stock_movements=Stock_Movements::find($id);

//     $stock_movements->quantity = $request->post('form_quantity');
// $stock_movements->type = $request->post('form_type');
// $stock_movements->reason = $request->post('form_reason');
// $stock_movements->reference_id = $request->post('form_reference_id');
// $stock_movements->warehouse_id = $request->post('form_warehouse_id');
// $stock_movements->product_id = $request->post('form_product_id');


//     $data = [
//             'quantity'        => $request->post('form_quantity'),'type'        => $request->post('form_type'),'reason'        => $request->post('form_reason'),'reference_id'        => $request->post('form_reference_id'),'warehouse_id'        => $request->post('form_warehouse_id'),'product_id'        => $request->post('form_product_id'),
//         ];

//     //if($stock_movements->save())
//     if ($this->stock_movementsRepository->update($id, $data))
//     {
//        return redirect()->route('stock_movements.listing')->with('success_message', 'Record has updated successfully.');
//     }else{
//            return redirect()->route('stock_movements.listing')->with('error_message', 'Error while updating record.');
//            //App::abort(500, 'Error');
//          }
  }

  public function destroy($id)
  {
    die('not allowed');
    //$stock_movements=Stock_Movements::find($id);
    
    //if($stock_movements->delete())
    if ($this->stock_movementsRepository->delete($id))
    {
       return redirect()->route('stock_movements.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('stock_movements.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::stock_movements/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Stock_Movements::with(['Warehouses', 'Products', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->warehouses)) { $btn = $row->warehouses->warehouse_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->products)) { $btn = $row->products->product_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('stock_movements.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('stock_movements.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('stock_movements.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
