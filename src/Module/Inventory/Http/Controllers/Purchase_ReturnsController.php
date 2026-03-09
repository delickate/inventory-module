<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Purchase_ReturnsRepositoryInterface;
use Modules\Inventory\Entities\Purchase_Returns;
use Modules\Inventory\Entities\Purchases;
use Modules\Inventory\Entities\Products;


use DB;
use DataTables;

class Purchase_ReturnsController extends Controller
{
    protected $purchase_returnsRepository;

    public function __construct(Purchase_ReturnsRepositoryInterface $purchase_returnsRepository)
    {
        $this->purchase_returnsRepository = $purchase_returnsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $purchase_returns = Purchase_Returns::with(['Purchases', 'Products', ])->get();
           
        //}else{
          //      $purchase_returns = Purchase_Returns::with(['Purchases', 'Products', ])->paginate($perPage);
          //   }

		//$data['purchase_returns'] = $purchase_returns; 

        $data['purchase_returns'] = $this->purchase_returnsRepository->getAll($perPage, $keyword);

        return view('inventory::purchase_returns/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['purchases'] = Purchases::orderBy('invoice_no')->pluck('invoice_no', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::purchase_returns/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_return_date' => 'required',
'form_total_amount' => 'required',
'form_reason' => 'required',
'form_purchase_id' => 'required',
'form_supplier_id' => 'required',

                        ]);

     $purchase_returns = new Purchase_Returns;
     $purchase_returns->return_date = $request->post('form_return_date');
$purchase_returns->total_amount = $request->post('form_total_amount');
$purchase_returns->reason = $request->post('form_reason');
$purchase_returns->purchase_id = $request->post('form_purchase_id');
$purchase_returns->supplier_id = $request->post('form_supplier_id');

     

     $data = [
            'return_date'        => $request->post('form_return_date'),'total_amount'        => $request->post('form_total_amount'),'reason'        => $request->post('form_reason'),'purchase_id'        => $request->post('form_purchase_id'),'supplier_id'        => $request->post('form_supplier_id'),
        ];

     //if($purchase_returns->save())
     if ($this->purchase_returnsRepository->create($data))
     {
        return redirect()->route('purchase_returns.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('purchase_returns.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['purchase_returns']=Purchase_Returns::with(['Purchases', 'Products', ])->find($id);
     $data['purchase_returns'] = $this->purchase_returnsRepository->findById($id);

     return view('inventory::purchase_returns/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['purchases'] = Purchases::orderBy('invoice_no')->pluck('invoice_no', 'id');
$data['products'] = Products::orderBy('name')->pluck('name', 'id');
		

     //$data['purchase_returns']= Purchase_Returns::find($id);

     $data['purchase_returns'] = $this->purchase_returnsRepository->findById($id);

     return view('inventory::purchase_returns/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_return_date' => 'required',
'form_total_amount' => 'required',
'form_reason' => 'required',
'form_purchase_id' => 'required',
'form_supplier_id' => 'required',
]);

    $purchase_returns=Purchase_Returns::find($id);

    $purchase_returns->return_date = $request->post('form_return_date');
$purchase_returns->total_amount = $request->post('form_total_amount');
$purchase_returns->reason = $request->post('form_reason');
$purchase_returns->purchase_id = $request->post('form_purchase_id');
$purchase_returns->supplier_id = $request->post('form_supplier_id');


    $data = [
            'return_date'        => $request->post('form_return_date'),'total_amount'        => $request->post('form_total_amount'),'reason'        => $request->post('form_reason'),'purchase_id'        => $request->post('form_purchase_id'),'supplier_id'        => $request->post('form_supplier_id'),
        ];

    //if($purchase_returns->save())
    if ($this->purchase_returnsRepository->update($id, $data))
    {
       return redirect()->route('purchase_returns.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('purchase_returns.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$purchase_returns=Purchase_Returns::find($id);
    
    //if($purchase_returns->delete())
    if ($this->purchase_returnsRepository->delete($id))
    {
       return redirect()->route('purchase_returns.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('purchase_returns.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::purchase_returns/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Purchase_Returns::with(['Purchases', 'Products', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->purchases)) { $btn = $row->purchases->purchase_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->products)) { $btn = $row->products->supplier_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('purchase_returns.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('purchase_returns.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('purchase_returns.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
