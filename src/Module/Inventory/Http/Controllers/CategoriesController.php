<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\CategoriesRepositoryInterface;
use Modules\Inventory\Entities\Categories;


use DB;
use DataTables;

class CategoriesController extends Controller
{
    protected $categoriesRepository;

    public function __construct(CategoriesRepositoryInterface $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $categories = Categories::with([])->get();
           
        //}else{
          //      $categories = Categories::with([])->paginate($perPage);
          //   }

		$data['title'] = "Categories"; 

        $data['categories'] = $this->categoriesRepository->getAll($perPage, $keyword);

        return view('inventory::categories/list', $data);
    }
    
    public function create()
    {
       $data = array();

       	

       return view('inventory::categories/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_name' => 'required',

                        ]);

     $categories = new Categories;
     $categories->name = $request->post('form_name');

     

     $data = [
            'name'        => $request->post('form_name'),
        ];

     //if($categories->save())
     if ($this->categoriesRepository->create($data))
     {
        return redirect()->route('categories.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('categories.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['categories']=Categories::with([])->find($id);
     $data['categories'] = $this->categoriesRepository->findById($id);

     return view('inventory::categories/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     		

     //$data['categories']= Categories::find($id);

     $data['categories'] = $this->categoriesRepository->findById($id);

     return view('inventory::categories/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_name' => 'required',
]);

    $categories=Categories::find($id);

    $categories->name = $request->post('form_name');


    $data = [
            'name'        => $request->post('form_name'),
        ];

    //if($categories->save())
    if ($this->categoriesRepository->update($id, $data))
    {
       return redirect()->route('categories.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('categories.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$categories=Categories::find($id);
    
    //if($categories->delete())
    if ($this->categoriesRepository->delete($id))
    {
       return redirect()->route('categories.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('categories.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::categories/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Categories::with([]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('categories.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('categories.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('categories.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
