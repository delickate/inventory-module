<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\UnitsRepositoryInterface;
use Modules\Inventory\Entities\Units;


use DB;
use DataTables;

class UnitsController extends Controller
{
    protected $unitsRepository;

    public function __construct(UnitsRepositoryInterface $unitsRepository)
    {
        $this->unitsRepository = $unitsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        $data['title'] = "Units"; 

        //if (!empty($keyword))
        //{

          //  $units = Units::with([])->get();
           
        //}else{
          //      $units = Units::with([])->paginate($perPage);
          //   }

		//$data['units'] = $units; 

        $data['units'] = $this->unitsRepository->getAll($perPage, $keyword);

        return view('inventory::units/list', $data);
    }
    
    public function create()
    {
       $data = array();

       	

       return view('inventory::units/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_name' => 'required',

                        ]);

     $units = new Units;
     $units->name = $request->post('form_name');

     

     $data = [
            'name'        => $request->post('form_name'),
        ];

     //if($units->save())
     if ($this->unitsRepository->create($data))
     {
        return redirect()->route('units.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('units.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['units']=Units::with([])->find($id);
     $data['units'] = $this->unitsRepository->findById($id);

     return view('inventory::units/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     		

     //$data['units']= Units::find($id);

     $data['units'] = $this->unitsRepository->findById($id);

     return view('inventory::units/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_name' => 'required',
]);

    $units=Units::find($id);

    $units->name = $request->post('form_name');


    $data = [
            'name'        => $request->post('form_name'),
        ];

    //if($units->save())
    if ($this->unitsRepository->update($id, $data))
    {
       return redirect()->route('units.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('units.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$units=Units::find($id);
    
    //if($units->delete())
    if ($this->unitsRepository->delete($id))
    {
       return redirect()->route('units.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('units.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::units/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Units::with([]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('units.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('units.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('units.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
