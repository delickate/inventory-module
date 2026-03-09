<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\CustomersRepositoryInterface;
use Modules\Inventory\Entities\Customers;


use DB;
use DataTables;

class CustomersController extends Controller
{
    protected $customersRepository;

    public function __construct(CustomersRepositoryInterface $customersRepository)
    {
        $this->customersRepository = $customersRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        $data["title"] = "Customers";

        //if (!empty($keyword))
        //{

          //  $customers = Customers::with([])->get();
           
        //}else{
          //      $customers = Customers::with([])->paginate($perPage);
          //   }

		//$data['customers'] = $customers; 

        $data['customers'] = $this->customersRepository->getAll($perPage, $keyword);

        return view('inventory::customers/list', $data);
    }
    
    public function create()
    {
       $data = array();

       	

       return view('inventory::customers/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_name' => 'required',
'form_phone' => 'required',
'form_address' => 'required',

                        ]);

     $customers = new Customers;
     $customers->name = $request->post('form_name');
$customers->phone = $request->post('form_phone');
$customers->address = $request->post('form_address');

     

     $data = [
            'name'        => $request->post('form_name'),'phone'        => $request->post('form_phone'),'address'        => $request->post('form_address'),
        ];

     //if($customers->save())
     if ($this->customersRepository->create($data))
     {
        return redirect()->route('customers.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('customers.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['customers']=Customers::with([])->find($id);
     $data['customers'] = $this->customersRepository->findById($id);

     return view('inventory::customers/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     		

     //$data['customers']= Customers::find($id);

     $data['customers'] = $this->customersRepository->findById($id);

     return view('inventory::customers/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_name' => 'required',
'form_phone' => 'required',
'form_address' => 'required',
]);

    $customers=Customers::find($id);

    $customers->name = $request->post('form_name');
$customers->phone = $request->post('form_phone');
$customers->address = $request->post('form_address');


    $data = [
            'name'        => $request->post('form_name'),'phone'        => $request->post('form_phone'),'address'        => $request->post('form_address'),
        ];

    //if($customers->save())
    if ($this->customersRepository->update($id, $data))
    {
       return redirect()->route('customers.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('customers.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$customers=Customers::find($id);
    
    //if($customers->delete())
    if ($this->customersRepository->delete($id))
    {
       return redirect()->route('customers.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('customers.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::customers/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Customers::with([]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('customers.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('customers.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('customers.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
