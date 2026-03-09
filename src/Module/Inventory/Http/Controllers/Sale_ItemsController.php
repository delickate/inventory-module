<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Sale_ItemsRepositoryInterface;
use Modules\Inventory\Entities\Sale_Items;
use Modules\Inventory\Entities\Sales;
use Modules\Inventory\Entities\Products;
use Modules\Inventory\Entities\Stock_Movements;
use Modules\Inventory\Entities\Inventory;

use DB;
use DataTables;

class Sale_ItemsController extends Controller
{
    protected $sale_itemsRepository;

    public function __construct(Sale_ItemsRepositoryInterface $sale_itemsRepository)
    {
        $this->sale_itemsRepository = $sale_itemsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $sale_items = Sale_Items::with(['Sales', 'Products', ])->get();
           
        //}else{
          //      $sale_items = Sale_Items::with(['Sales', 'Products', ])->paginate($perPage);
          //   }

		//$data['sale_items'] = $sale_items; 

        $data['sale_items'] = $this->sale_itemsRepository->getAll($perPage, $keyword);

        return view('inventory::sale_items/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['sales'] = Sales::orderBy('date')->pluck('date', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::sale_items/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_quantity' => 'required',
'form_unit_price' => 'required',
//'form_subtotal' => 'required',
'form_sale_id' => 'required',
'form_product_id' => 'required',

                        ]);


     try {
    DB::transaction(function () use ($request) {

        $quantity = $request->post('form_quantity');
        $unitPrice = $request->post('form_unit_price');
        $saleId = $request->post('form_sale_id');
        $productId = $request->post('form_product_id');
        $subtotal = $quantity * $unitPrice;
        $userId = auth()->id(); // or use your method to get logged-in user

        // 1. Insert into sales_items
        $saleItem = Sale_Items::create([
            'quantity'   => $quantity,
            'unit_price' => $unitPrice,
            'subtotal'   => $subtotal,
            'sale_id'    => $saleId,
            'product_id' => $productId,
        ]);

        // 2. Update inventory (reduce quantity)
        Inventory::where('product_id', $productId)
            ->decrement('quantity', $quantity);

        // 3. Insert into stock_movements
        Stock_Movements::create([
            'product_id'   => $productId,
            'type'         => 'sale',
            'quantity'     => -$quantity, // Negative quantity for sale
            'reason'       => 'Product sold',
            'reference_id' => $saleId,
            'created_by'   => $userId,
        ]);
    });

    return redirect()->route('sale_items.listing')
                     ->with('success_message', 'Sale item and stock updated successfully.');

} catch (\Exception $e) {
    return redirect()->route('sale_items.listing')
                     ->with('error_message', 'Error: ' . $e->getMessage());
}


//      $sale_items = new Sale_Items;
//      $sale_items->quantity = $request->post('form_quantity');
// $sale_items->unit_price = $request->post('form_unit_price');
// $sale_items->subtotal = ($request->post('form_quantity')*$request->post('form_unit_price'));
// $sale_items->sale_id = $request->post('form_sale_id');
// $sale_items->product_id = $request->post('form_product_id');

     

//      $data = [
//             'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'subtotal'        => ($request->post('form_quantity')*$request->post('form_unit_price')),'sale_id'        => $request->post('form_sale_id'),'product_id'        => $request->post('form_product_id'),
//         ];

//      //if($sale_items->save())
//      if ($this->sale_itemsRepository->create($data))
//      {
//         return redirect()->route('sale_items.listing')->with('success_message', 'Password has been saved successfully.');
//      }else{
//              return redirect()->route('sale_items.listing')->with('error_message', 'Error while saving record.');
//              //App::abort(500, 'Error');
//          }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['sale_items']=Sale_Items::with(['Sales', 'Products', ])->find($id);
     $data['sale_items'] = $this->sale_itemsRepository->findById($id);

     return view('inventory::sale_items/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['sales'] = Sales::orderBy('date')->pluck('date', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
		

     //$data['sale_items']= Sale_Items::find($id);

     $data['sale_items'] = $this->sale_itemsRepository->findById($id);

     return view('inventory::sale_items/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_quantity' => 'required',
'form_unit_price' => 'required',
//'form_subtotal' => 'required',
'form_sale_id' => 'required',
'form_product_id' => 'required',
]);

    $sale_items=Sale_Items::find($id);


    try {
    DB::transaction(function () use ($request, $id) {
        $newQty = $request->post('form_quantity');
        $newUnitPrice = $request->post('form_unit_price');
        $newSubtotal = $newQty * $newUnitPrice;
        $newProductId = $request->post('form_product_id');
        $saleId = $request->post('form_sale_id');
        $userId = auth()->id();

        // 1. Fetch the original sale item
        $saleItem = Sale_Items::findOrFail($id);
        $oldQty = $saleItem->quantity;
        $oldProductId = $saleItem->product_id;

        // 2. Update sale item
        $saleItem->update([
            'quantity'   => $newQty,
            'unit_price' => $newUnitPrice,
            'subtotal'   => $newSubtotal,
            'sale_id'    => $saleId,
            'product_id' => $newProductId,
        ]);

        // 3. Update inventory
        if ($oldProductId == $newProductId) {
            // Same product, adjust quantity
            $difference = $oldQty - $newQty; // e.g., 5 → 3 = reduce by 2
            Inventory::where('product_id', $newProductId)
                ->increment('quantity', $difference); // positive = add back stock, negative = deduct
        } else {
            // Different product selected
            Inventory::where('product_id', $oldProductId)
                ->increment('quantity', $oldQty); // Revert old product's stock
            Inventory::where('product_id', $newProductId)
                ->decrement('quantity', $newQty); // Deduct new product's stock
        }

        // 4. Update stock movement
        Stock_Movements::where([
            'product_id' => $oldProductId,
            'reference_id' => $saleId,
            'type' => 'sale'
        ])->update([
            'product_id' => $newProductId,
            'quantity'   => -$newQty, // always store sale as negative
            'reason'     => 'Product sold (updated)',
            'created_by' => $userId,
        ]);
    });

    return redirect()->route('sale_items.listing')
                     ->with('success_message', 'Sale item updated successfully and inventory adjusted.');
} catch (\Exception $e) {
    return redirect()->route('sale_items.listing')
                     ->with('error_message', 'Error while updating: ' . $e->getMessage());
}


//     $sale_items->quantity = $request->post('form_quantity');
// $sale_items->unit_price = $request->post('form_unit_price');
// $sale_items->subtotal = ($request->post('form_quantity')*$request->post('form_unit_price'));
// $sale_items->sale_id = $request->post('form_sale_id');
// $sale_items->product_id = $request->post('form_product_id');


//     $data = [
//             'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'subtotal'        => ($request->post('form_quantity')*$request->post('form_unit_price')),'sale_id'        => $request->post('form_sale_id'),'product_id'        => $request->post('form_product_id'),
//         ];

//     //if($sale_items->save())
//     if ($this->sale_itemsRepository->update($id, $data))
//     {
//        return redirect()->route('sale_items.listing')->with('success_message', 'Record has updated successfully.');
//     }else{
//            return redirect()->route('sale_items.listing')->with('error_message', 'Error while updating record.');
//            //App::abort(500, 'Error');
//          }
  }

  public function destroy($id)
  {

    //$sale_items=Sale_Items::find($id);
    
    //if($sale_items->delete())
    if ($this->sale_itemsRepository->delete($id))
    {
       return redirect()->route('sale_items.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('sale_items.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::sale_items/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Sale_Items::with(['Sales', 'Products', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->sales)) { $btn = $row->sales->sale_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->products)) { $btn = $row->products->product_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('sale_items.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('sale_items.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('sale_items.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
