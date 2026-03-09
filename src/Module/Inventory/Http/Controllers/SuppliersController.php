<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\SuppliersRepositoryInterface;
use Modules\Inventory\Entities\Suppliers;


use DB;
use DataTables;

class SuppliersController extends Controller
{
    protected $suppliersRepository;

    public function __construct(SuppliersRepositoryInterface $suppliersRepository)
    {
        $this->suppliersRepository = $suppliersRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        $data['title'] = "Suppliers"; 

        //if (!empty($keyword))
        //{

          //  $suppliers = Suppliers::with([])->get();
           
        //}else{
          //      $suppliers = Suppliers::with([])->paginate($perPage);
          //   }

		//$data['suppliers'] = $suppliers; 

        $data['suppliers'] = $this->suppliersRepository->getAll($perPage, $keyword);

        return view('inventory::suppliers/list', $data);
    }
    
    public function create()
    {
       $data = array();

       	

       return view('inventory::suppliers/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_name' => 'required',
'form_email' => 'required',
'form_address' => 'required',

                        ]);

     $suppliers = new Suppliers;
     $suppliers->name = $request->post('form_name');
$suppliers->email = $request->post('form_email');
$suppliers->address = $request->post('form_address');

     

     $data = [
            'name'        => $request->post('form_name'),'email'        => $request->post('form_email'),'address'        => $request->post('form_address'),
        ];

     //if($suppliers->save())
     if ($this->suppliersRepository->create($data))
     {
        return redirect()->route('suppliers.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('suppliers.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['suppliers']=Suppliers::with([])->find($id);
     $data['suppliers'] = $this->suppliersRepository->findById($id);

     return view('inventory::suppliers/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     		

     //$data['suppliers']= Suppliers::find($id);

     $data['suppliers'] = $this->suppliersRepository->findById($id);

     return view('inventory::suppliers/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_name' => 'required',
'form_email' => 'required',
'form_address' => 'required',
]);

    $suppliers=Suppliers::find($id);

    $suppliers->name = $request->post('form_name');
$suppliers->email = $request->post('form_email');
$suppliers->address = $request->post('form_address');


    $data = [
            'name'        => $request->post('form_name'),'email'        => $request->post('form_email'),'address'        => $request->post('form_address'),
        ];

    //if($suppliers->save())
    if ($this->suppliersRepository->update($id, $data))
    {
       return redirect()->route('suppliers.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('suppliers.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$suppliers=Suppliers::find($id);
    
    //if($suppliers->delete())
    if ($this->suppliersRepository->delete($id))
    {
       return redirect()->route('suppliers.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('suppliers.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::suppliers/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Suppliers::with([]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('email', function($row){ $btn = $row->email; return $btn; })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('suppliers.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('suppliers.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('suppliers.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
