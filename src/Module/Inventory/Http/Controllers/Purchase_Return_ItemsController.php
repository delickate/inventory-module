<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Purchase_Return_ItemsRepositoryInterface;
use Modules\Inventory\Entities\Purchase_Return_Items;
use Modules\Inventory\Entities\Purchase_Returns;
use Modules\Inventory\Entities\Products;
use DB;
use Modules\Inventory\Entities\StockMovement;
use Modules\Inventory\Entities\Inventory;


use DataTables;

class Purchase_Return_ItemsController extends Controller
{
    protected $purchase_return_itemsRepository;

    public function __construct(Purchase_Return_ItemsRepositoryInterface $purchase_return_itemsRepository)
    {
        $this->purchase_return_itemsRepository = $purchase_return_itemsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $purchase_return_items = Purchase_Return_Items::with(['Purchase_Returns', 'Products', ])->get();
           
        //}else{
          //      $purchase_return_items = Purchase_Return_Items::with(['Purchase_Returns', 'Products', ])->paginate($perPage);
          //   }

		//$data['purchase_return_items'] = $purchase_return_items; 

        $data['purchase_return_items'] = $this->purchase_return_itemsRepository->getAll($perPage, $keyword);

        return view('inventory::purchase_return_items/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['purchase_returns'] = Purchase_Returns::orderBy('invoice_no')->pluck('invoice_no', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::purchase_return_items/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_quantity' => 'required',
'form_unit_price' => 'required',
'form_unit_price' => 'required',
'form_purchase_return_id' => 'required',
'form_product_id' => 'required',

                        ]);



try {
    DB::transaction(function () use ($request) {

        $qty = $request->post('form_quantity');
        $unitPrice = $request->post('form_unit_price');
        $purchaseReturnId = $request->post('form_purchase_return_id');
        $productId = $request->post('form_product_id');
        $userId = auth()->id();

        // 1. Create purchase return item
        $data = [
            'quantity'             => $qty,
            'unit_price'           => $unitPrice,
            'subtotal'             => $qty * $unitPrice,
            'purchase_return_id'   => $purchaseReturnId,
            'product_id'           => $productId,
        ];

        $this->purchase_return_itemsRepository->create($data);

        // 2. Update inventory (deduct quantity)
        Inventory::where('product_id', $productId)->decrement('quantity', $qty);

        // 3. Create stock movement record
        StockMovement::create([
            'product_id'    => $productId,
            'type'          => 'purchase_return',
            'quantity'      => -$qty, // negative to reduce stock
            'reason'        => 'Purchase return',
            'reference_id'  => $purchaseReturnId, // optional, if you have this column
            'created_by'    => $userId,
        ]);
    });

    return redirect()->route('purchase_return_items.listing')->with('success_message', 'Purchase return item saved and stock updated successfully.');

} catch (\Exception $e) {
    return redirect()->route('purchase_return_items.listing')->with('error_message', 'Error: ' . $e->getMessage());
}


//      $purchase_return_items = new Purchase_Return_Items;
//      $purchase_return_items->quantity = $request->post('form_quantity');
// $purchase_return_items->unit_price = $request->post('form_unit_price');
// $purchase_return_items->unit_price = $request->post('form_unit_price');
// $purchase_return_items->purchase_return_id = $request->post('form_purchase_return_id');
// $purchase_return_items->product_id = $request->post('form_product_id');

     

//      $data = [
//             'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'unit_price'        => $request->post('form_unit_price'),'purchase_return_id'        => $request->post('form_purchase_return_id'),'product_id'        => $request->post('form_product_id'),
//         ];

//      //if($purchase_return_items->save())
//      if ($this->purchase_return_itemsRepository->create($data))
//      {
//         return redirect()->route('purchase_return_items.listing')->with('success_message', 'Password has been saved successfully.');
//      }else{
//              return redirect()->route('purchase_return_items.listing')->with('error_message', 'Error while saving record.');
//              //App::abort(500, 'Error');
//          }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['purchase_return_items']=Purchase_Return_Items::with(['Purchase_Returns', 'Products', ])->find($id);
     $data['purchase_return_items'] = $this->purchase_return_itemsRepository->findById($id);

     return view('inventory::purchase_return_items/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['purchase_returns'] = Purchase_Returns::orderBy('invoice_no')->pluck('invoice_no', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
		

     //$data['purchase_return_items']= Purchase_Return_Items::find($id);

     $data['purchase_return_items'] = $this->purchase_return_itemsRepository->findById($id);

     return view('inventory::purchase_return_items/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_quantity' => 'required',
'form_unit_price' => 'required',
'form_unit_price' => 'required',
'form_purchase_return_id' => 'required',
'form_product_id' => 'required',
]);

    $purchase_return_items=Purchase_Return_Items::find($id);

    $purchase_return_items->quantity = $request->post('form_quantity');
$purchase_return_items->unit_price = $request->post('form_unit_price');
$purchase_return_items->unit_price = $request->post('form_unit_price');
$purchase_return_items->purchase_return_id = $request->post('form_purchase_return_id');
$purchase_return_items->product_id = $request->post('form_product_id');


    $data = [
            'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'unit_price'        => $request->post('form_unit_price'),'purchase_return_id'        => $request->post('form_purchase_return_id'),'product_id'        => $request->post('form_product_id'),
        ];

    //if($purchase_return_items->save())
    if ($this->purchase_return_itemsRepository->update($id, $data))
    {
       return redirect()->route('purchase_return_items.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('purchase_return_items.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$purchase_return_items=Purchase_Return_Items::find($id);
    
    //if($purchase_return_items->delete())
    if ($this->purchase_return_itemsRepository->delete($id))
    {
       return redirect()->route('purchase_return_items.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('purchase_return_items.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::purchase_return_items/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Purchase_Return_Items::with(['Purchase_Returns', 'Products', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->purchase_returns)) { $btn = $row->purchase_returns->purchase_return_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->products)) { $btn = $row->products->product_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('purchase_return_items.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('purchase_return_items.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('purchase_return_items.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
