<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\Sale_ReturnsRepositoryInterface;
use Modules\Inventory\Entities\Sale_Returns;
use Modules\Inventory\Entities\Sales;
use Modules\Inventory\Entities\Customers;


use DB;
use DataTables;

class Sale_ReturnsController extends Controller
{
    protected $sale_returnsRepository;

    public function __construct(Sale_ReturnsRepositoryInterface $sale_returnsRepository)
    {
        $this->sale_returnsRepository = $sale_returnsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $sale_returns = Sale_Returns::with(['Sales', 'Customers', ])->get();
           
        //}else{
          //      $sale_returns = Sale_Returns::with(['Sales', 'Customers', ])->paginate($perPage);
          //   }

		//$data['sale_returns'] = $sale_returns; 

        $data['sale_returns'] = $this->sale_returnsRepository->getAll($perPage, $keyword);

        return view('inventory::sale_returns/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['sales'] = Sales::orderBy('name')->pluck('name', 'id');
$data['customers'] = Customers::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::sale_returns/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_return_date' => 'required',
'form_total_amount' => 'required',
'form_reason' => 'required',
'form_sale_id' => 'required',
'form_customer_id' => 'required',

                        ]);

     $sale_returns = new Sale_Returns;
     $sale_returns->return_date = $request->post('form_return_date');
$sale_returns->total_amount = $request->post('form_total_amount');
$sale_returns->reason = $request->post('form_reason');
$sale_returns->sale_id = $request->post('form_sale_id');
$sale_returns->customer_id = $request->post('form_customer_id');

     

     $data = [
            'return_date'        => $request->post('form_return_date'),'total_amount'        => $request->post('form_total_amount'),'reason'        => $request->post('form_reason'),'sale_id'        => $request->post('form_sale_id'),'customer_id'        => $request->post('form_customer_id'),
        ];

     //if($sale_returns->save())
     if ($this->sale_returnsRepository->create($data))
     {
        return redirect()->route('sale_returns.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('sale_returns.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['sale_returns']=Sale_Returns::with(['Sales', 'Customers', ])->find($id);
     $data['sale_returns'] = $this->sale_returnsRepository->findById($id);

     return view('inventory::sale_returns/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['sales'] = Sales::orderBy('name')->pluck('name', 'id');
$data['customers'] = Customers::orderBy('name')->pluck('name', 'id');
		

     //$data['sale_returns']= Sale_Returns::find($id);

     $data['sale_returns'] = $this->sale_returnsRepository->findById($id);

     return view('inventory::sale_returns/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_return_date' => 'required',
'form_total_amount' => 'required',
'form_reason' => 'required',
'form_sale_id' => 'required',
'form_customer_id' => 'required',
]);

    $sale_returns=Sale_Returns::find($id);

    $sale_returns->return_date = $request->post('form_return_date');
$sale_returns->total_amount = $request->post('form_total_amount');
$sale_returns->reason = $request->post('form_reason');
$sale_returns->sale_id = $request->post('form_sale_id');
$sale_returns->customer_id = $request->post('form_customer_id');


    $data = [
            'return_date'        => $request->post('form_return_date'),'total_amount'        => $request->post('form_total_amount'),'reason'        => $request->post('form_reason'),'sale_id'        => $request->post('form_sale_id'),'customer_id'        => $request->post('form_customer_id'),
        ];

    //if($sale_returns->save())
    if ($this->sale_returnsRepository->update($id, $data))
    {
       return redirect()->route('sale_returns.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('sale_returns.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$sale_returns=Sale_Returns::find($id);
    
    //if($sale_returns->delete())
    if ($this->sale_returnsRepository->delete($id))
    {
       return redirect()->route('sale_returns.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('sale_returns.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::sale_returns/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Sale_Returns::with(['Sales', 'Customers', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->sales)) { $btn = $row->sales->sale_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->customers)) { $btn = $row->customers->customer_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('sale_returns.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('sale_returns.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('sale_returns.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
