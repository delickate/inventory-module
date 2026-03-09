<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\WarehousesRepositoryInterface;
use Modules\Inventory\Entities\Warehouses;


use DB;
use DataTables;

class WarehousesController extends Controller
{
    protected $warehousesRepository;

    public function __construct(WarehousesRepositoryInterface $warehousesRepository)
    {
        $this->warehousesRepository = $warehousesRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        $data["title"] = "Warehouses";

        //if (!empty($keyword))
        //{

          //  $warehouses = Warehouses::with([])->get();
           
        //}else{
          //      $warehouses = Warehouses::with([])->paginate($perPage);
          //   }

		//$data['warehouses'] = $warehouses; 

        $data['warehouses'] = $this->warehousesRepository->getAll($perPage, $keyword);

        return view('inventory::warehouses/list', $data);
    }
    
    public function create()
    {
       $data = array();

       	

       return view('inventory::warehouses/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_name' => 'required',

                        ]);

     $warehouses = new Warehouses;
     $warehouses->name = $request->post('form_name');

     

     $data = [
            'name'        => $request->post('form_name'),
        ];

     //if($warehouses->save())
     if ($this->warehousesRepository->create($data))
     {
        return redirect()->route('warehouses.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('warehouses.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['warehouses']=Warehouses::with([])->find($id);
     $data['warehouses'] = $this->warehousesRepository->findById($id);

     return view('inventory::warehouses/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     		

     //$data['warehouses']= Warehouses::find($id);

     $data['warehouses'] = $this->warehousesRepository->findById($id);

     return view('inventory::warehouses/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_name' => 'required',
]);

    $warehouses=Warehouses::find($id);

    $warehouses->name = $request->post('form_name');


    $data = [
            'name'        => $request->post('form_name'),
        ];

    //if($warehouses->save())
    if ($this->warehousesRepository->update($id, $data))
    {
       return redirect()->route('warehouses.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('warehouses.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$warehouses=Warehouses::find($id);
    
    //if($warehouses->delete())
    if ($this->warehousesRepository->delete($id))
    {
       return redirect()->route('warehouses.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('warehouses.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::warehouses/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Warehouses::with([]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('name', function($row){ $btn = $row->name; return $btn; })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('warehouses.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('warehouses.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('warehouses.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
