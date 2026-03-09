<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Purchase_ItemsRepositoryInterface;
use Modules\Inventory\Entities\Purchase_Items;
use Modules\Inventory\Entities\Purchases;
use Modules\Inventory\Entities\Products;
use Modules\Inventory\Entities\Stock_Movements;
use Modules\Inventory\Entities\Inventory;


use DB;
use DataTables;

class Purchase_ItemsController extends Controller
{
    protected $purchase_itemsRepository;

    public function __construct(Purchase_ItemsRepositoryInterface $purchase_itemsRepository)
    {
        $this->purchase_itemsRepository = $purchase_itemsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $purchase_items = Purchase_Items::with(['Purchases', 'Products', ])->get();
           
        //}else{
          //      $purchase_items = Purchase_Items::with(['Purchases', 'Products', ])->paginate($perPage);
          //   }

		//$data['purchase_items'] = $purchase_items; 

        $data['purchase_items'] = $this->purchase_itemsRepository->getAll($perPage, $keyword);

        return view('inventory::purchase_items/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['purchases'] = Purchases::orderBy('invoice_no')->pluck('invoice_no', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::purchase_items/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_purchase_id' => 'required',
'form_product_id' => 'required',
'form_quantity' => 'required',
'form_unit_price' => 'required',
//'form_subtotal' => 'required',

                        ]);

 $productId = $request->post('form_product_id');
 $purchaseId = $request->post('form_purchase_id');
 $qty = $request->post('form_quantity');
 $userId = 1;   
 $unitPrice = $request->post('form_unit_price');   

try {
    DB::transaction(function () use ($productId, $purchaseId, $qty, $userId, $unitPrice) {
        
        $item = Purchase_Items::where('product_id', $productId)
                              ->where('purchase_id', $purchaseId)
                              ->first();

        if ($item) {
            $item->quantity += $qty;
            $item->unit_price = $unitPrice;
            //$item->subtotal = $item->quantity * $unitPrice; // Only if subtotal is not generated
            $item->save();
        } else {
            Purchase_Items::create([
                'product_id'   => $productId,
                'purchase_id'  => $purchaseId,
                'quantity'     => $qty,
                'unit_price'   => $unitPrice,
                //'subtotal'     => $qty * $unitPrice, // Skip if subtotal is a generated column
            ]);
        }

        Stock_Movements::create([
            'product_id'    => $productId,
            'purchase_id'   => $purchaseId,
            'type'          => 'purchase',
            'quantity'      => $qty,
            'reason'        => 'Initial stock',
            'created_by'    => $userId,
        ]);
    });

    return redirect()->route('purchase_items.listing')->with('success_message', 'Stock has been saved successfully.');

} catch (\Exception $e) {
    return redirect()->route('purchase_items.listing')->with('error_message', $e->getMessage());
}



//      $purchase_items = new Purchase_Items;
//      $purchase_items->purchase_id = $request->post('form_purchase_id');
// $purchase_items->product_id = $request->post('form_product_id');
// $purchase_items->quantity = $request->post('form_quantity');
// $purchase_items->unit_price = $request->post('form_unit_price');
// //$purchase_items->subtotal = ($request->post('form_quantity')*$request->post('form_unit_price'));

     

//      $data = [
//             'purchase_id'        => $request->post('form_purchase_id'),
//             'product_id'        => $request->post('form_product_id'),
//             'quantity'        => $request->post('form_quantity'),
//             'unit_price'        => $request->post('form_unit_price'),
//            // 'subtotal'        => ($request->post('form_quantity')*$request->post('form_unit_price')),
//         ];

//      //if($purchase_items->save())
//      if ($this->purchase_itemsRepository->create($data))
//      {
//         return redirect()->route('purchase_items.listing')->with('success_message', 'Password has been saved successfully.');
//      }else{
//              return redirect()->route('purchase_items.listing')->with('error_message', 'Error while saving record.');
//              //App::abort(500, 'Error');
//          }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['purchase_items']=Purchase_Items::with(['Purchases', 'Products', ])->find($id);
     $data['purchase_items'] = $this->purchase_itemsRepository->findById($id);

     return view('inventory::purchase_items/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['purchases'] = Purchases::orderBy('invoice_no')->pluck('invoice_no', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
		

     //$data['purchase_items']= Purchase_Items::find($id);

     $data['purchase_items'] = $this->purchase_itemsRepository->findById($id);

     return view('inventory::purchase_items/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_purchase_id' => 'required',
'form_product_id' => 'required',
'form_quantity' => 'required',
'form_unit_price' => 'required',
//'form_subtotal' => 'required',
]);

    $purchaseItemId = $id;
    $productId = $request->post('form_product_id');
     $purchaseId = $request->post('form_purchase_id');
     $newQty = $request->post('form_quantity');
     $userId = 1;   
     $newPrice = $request->post('form_unit_price');   

     try {
    DB::transaction(function () use ($purchaseItemId, $newQty, $newPrice) {
    $item = Purchase_Items::findOrFail($purchaseItemId);

    $diffQty = $newQty - $item->quantity;

    // Update purchase item
    $item->update([
        'quantity' => $newQty,
        'unit_price' => $newPrice,
       // 'subtotal' => $newQty * $newPrice,
    ]);

    // Adjust inventory
    Inventory::where('product_id', $item->product_id)
        ->update([
            'quantity' => DB::raw("quantity + ($diffQty)")
        ]);

    // Add movement log
    Stock_Movements::create([
        'product_id' => $item->product_id,
        'type' => 'adjustment',
        'quantity' => $diffQty,
        'reason' => 'Purchase item update',
        'created_by' => auth()->id(),
    ]);
});

 return redirect()->route('purchase_items.listing')->with('success_message', 'Stock has been updated successfully.');

} catch (\Exception $e) {
    return redirect()->route('purchase_items.listing')->with('error_message', $e->getMessage());
}


//     $purchase_items=Purchase_Items::find($id);

//     $purchase_items->purchase_id = $request->post('form_purchase_id');
// $purchase_items->product_id = $request->post('form_product_id');
// $purchase_items->quantity = $request->post('form_quantity');
// $purchase_items->unit_price = $request->post('form_unit_price');
// $purchase_items->subtotal = ($request->post('form_quantity')*$request->post('form_unit_price'));


//     $data = [
//             'purchase_id'        => $request->post('form_purchase_id'),'product_id'        => $request->post('form_product_id'),'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'subtotal'        => ($request->post('form_quantity')*$request->post('form_unit_price')),
//         ];

//     //if($purchase_items->save())
//     if ($this->purchase_itemsRepository->update($id, $data))
//     {
//        return redirect()->route('purchase_items.listing')->with('success_message', 'Record has updated successfully.');
//     }else{
//            return redirect()->route('purchase_items.listing')->with('error_message', 'Error while updating record.');
//            //App::abort(500, 'Error');
//          }
  }

  public function destroy($id)
  {

    //$purchase_items=Purchase_Items::find($id);
    
    //if($purchase_items->delete())
    if ($this->purchase_itemsRepository->delete($id))
    {
       return redirect()->route('purchase_items.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('purchase_items.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::purchase_items/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Purchase_Items::with(['Purchases', 'Products', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->purchases)) { $btn = $row->purchases->purchase_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->products)) { $btn = $row->products->product_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('purchase_items.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('purchase_items.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('purchase_items.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
