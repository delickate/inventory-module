<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Sale_Return_ItemsRepositoryInterface;
use Modules\Inventory\Entities\Sale_Return_Items;
use Modules\Inventory\Entities\Sale_Returns;
use Modules\Inventory\Entities\Products;


use DB;
use DataTables;

class Sale_Return_ItemsController extends Controller
{
    protected $sale_return_itemsRepository;

    public function __construct(Sale_Return_ItemsRepositoryInterface $sale_return_itemsRepository)
    {
        $this->sale_return_itemsRepository = $sale_return_itemsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $sale_return_items = Sale_Return_Items::with(['Sale_Returns', 'Products', ])->get();
           
        //}else{
          //      $sale_return_items = Sale_Return_Items::with(['Sale_Returns', 'Products', ])->paginate($perPage);
          //   }

		//$data['sale_return_items'] = $sale_return_items; 

        $data['sale_return_items'] = $this->sale_return_itemsRepository->getAll($perPage, $keyword);

        return view('inventory::sale_return_items/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['sale_returns'] = Sale_Returns::orderBy('name')->pluck('name', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::sale_return_items/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_quantity' => 'required',
'form_unit_price' => 'required',
'form_subtotal' => 'required',
'form_sale_return_id' => 'required',
'form_product_id' => 'required',

                        ]);

     $sale_return_items = new Sale_Return_Items;
     $sale_return_items->quantity = $request->post('form_quantity');
$sale_return_items->unit_price = $request->post('form_unit_price');
$sale_return_items->subtotal = $request->post('form_subtotal');
$sale_return_items->sale_return_id = $request->post('form_sale_return_id');
$sale_return_items->product_id = $request->post('form_product_id');

     

     $data = [
            'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'subtotal'        => $request->post('form_subtotal'),'sale_return_id'        => $request->post('form_sale_return_id'),'product_id'        => $request->post('form_product_id'),
        ];

     //if($sale_return_items->save())
     if ($this->sale_return_itemsRepository->create($data))
     {
        return redirect()->route('sale_return_items.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('sale_return_items.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['sale_return_items']=Sale_Return_Items::with(['Sale_Returns', 'Products', ])->find($id);
     $data['sale_return_items'] = $this->sale_return_itemsRepository->findById($id);

     return view('inventory::sale_return_items/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['sale_returns'] = Sale_Returns::orderBy('name')->pluck('name', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
		

     //$data['sale_return_items']= Sale_Return_Items::find($id);

     $data['sale_return_items'] = $this->sale_return_itemsRepository->findById($id);

     return view('inventory::sale_return_items/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_quantity' => 'required',
'form_unit_price' => 'required',
'form_subtotal' => 'required',
'form_sale_return_id' => 'required',
'form_product_id' => 'required',
]);

    $sale_return_items=Sale_Return_Items::find($id);

    $sale_return_items->quantity = $request->post('form_quantity');
$sale_return_items->unit_price = $request->post('form_unit_price');
$sale_return_items->subtotal = $request->post('form_subtotal');
$sale_return_items->sale_return_id = $request->post('form_sale_return_id');
$sale_return_items->product_id = $request->post('form_product_id');


    $data = [
            'quantity'        => $request->post('form_quantity'),'unit_price'        => $request->post('form_unit_price'),'subtotal'        => $request->post('form_subtotal'),'sale_return_id'        => $request->post('form_sale_return_id'),'product_id'        => $request->post('form_product_id'),
        ];

    //if($sale_return_items->save())
    if ($this->sale_return_itemsRepository->update($id, $data))
    {
       return redirect()->route('sale_return_items.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('sale_return_items.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$sale_return_items=Sale_Return_Items::find($id);
    
    //if($sale_return_items->delete())
    if ($this->sale_return_itemsRepository->delete($id))
    {
       return redirect()->route('sale_return_items.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('sale_return_items.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::sale_return_items/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Sale_Return_Items::with(['Sale_Returns', 'Products', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->sale_returns)) { $btn = $row->sale_returns->sale_return_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->products)) { $btn = $row->products->product_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('sale_return_items.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('sale_return_items.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('sale_return_items.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
