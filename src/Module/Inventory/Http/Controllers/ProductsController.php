<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\ProductsRepositoryInterface;
use Modules\Inventory\Entities\Products;
use Modules\Inventory\Entities\Categories;
use Modules\Inventory\Entities\Units;


use DB;
use DataTables;

class ProductsController extends Controller
{
    protected $productsRepository;

    public function __construct(ProductsRepositoryInterface $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        $data["title"] = "Products";

        //if (!empty($keyword))
        //{

          //  $products = Products::with(['Categories', 'Units', ])->get();
           
        //}else{
          //      $products = Products::with(['Categories', 'Units', ])->paginate($perPage);
          //   }

		//$data['products'] = $products; 

        $data['products'] = $this->productsRepository->getAll($perPage, $keyword);

        return view('inventory::products/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['categories'] = Categories::orderBy('name')->pluck('name', 'id');
        $data['units'] = Units::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::products/add', $data);
   }

   public function store(Request $request)
   {

     $data = array();

     $request->validate([
                            'form_name'         => 'required',
                            'form_sku'          => 'required',
                            'form_barcode'      => 'required',
                            'form_category_id'  => 'required',
                            'form_unit_id'      => 'required',
                            'form_cost_price'   => 'required|numeric|min:0',
                            'form_sale_price'   => 'required|numeric|gt:form_cost_price',
                            'form_description'  => 'required',
                            //'form_image' => 'required',
                            //'form_status' => 'required',

                        ]);

     

     $data = [
                'name'          => $request->post('form_name'),
                'sku'           => $request->post('form_sku'),
                'barcode'       => $request->post('form_barcode'),
                'category_id'   => $request->post('form_category_id'),
                'unit_id'       => $request->post('form_unit_id'),
                'cost_price'    => $request->post('form_cost_price'),
                'sale_price'    => $request->post('form_sale_price'),
                'description'   => $request->post('form_description'),
                //'image'       => $request->post('form_image'),
                //'status'       => $request->post('form_status'),
            ];

     //if($products->save())
     if ($this->productsRepository->create($data))
     {
        

        return redirect()->route('products.listing')->with('success_message', 'Password has been saved successfully.');
     }else{
             return redirect()->route('products.listing')->with('error_message', 'Error while saving record.');
             //App::abort(500, 'Error');
         }
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['products']=Products::with(['Categories', 'Units', ])->find($id);
     $data['products'] = $this->productsRepository->findById($id);

     return view('inventory::products/show', $data);
  }

  public function edit($id)
  {
     $data = array();
	
     $data['categories'] = Categories::orderBy('name')->pluck('name', 'id');
$data['units'] = Units::orderBy('name')->pluck('name', 'id');
		

     //$data['products']= Products::find($id);

     $data['products'] = $this->productsRepository->findById($id);

     return view('inventory::products/edit', $data);

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $request->validate(['form_name' => 'required',
'form_sku' => 'required',
'form_barcode' => 'required',
'form_category_id' => 'required',
'form_unit_id' => 'required',
'form_cost_price' => 'required|numeric|min:0',
'form_sale_price' => 'required|numeric|gt:form_cost_price',
'form_description' => 'required',
//'form_image' => 'required',
//'form_status' => 'required',
]);

    $products=Products::find($id);

    $products->name = $request->post('form_name');
$products->sku = $request->post('form_sku');
$products->barcode = $request->post('form_barcode');
$products->category_id = $request->post('form_category_id');
$products->unit_id = $request->post('form_unit_id');
$products->cost_price = $request->post('form_cost_price');
$products->sale_price = $request->post('form_sale_price');
$products->description = $request->post('form_description');
//$products->image = $request->post('form_image');
//$products->status = $request->post('form_status');


    $data = [
            'name'        => $request->post('form_name'),
            'sku'        => $request->post('form_sku'),
            'barcode'        => $request->post('form_barcode'),
            'category_id'        => $request->post('form_category_id'),
            'unit_id'        => $request->post('form_unit_id'),
            'cost_price'        => $request->post('form_cost_price'),
            'sale_price'        => $request->post('form_sale_price'),
            'description'        => $request->post('form_description'),
            //'image'        => $request->post('form_image'),
            //'status'        => $request->post('form_status'),
        ];

    //if($products->save())
    if ($this->productsRepository->update($id, $data))
    {
       return redirect()->route('products.listing')->with('success_message', 'Record has updated successfully.');
    }else{
           return redirect()->route('products.listing')->with('error_message', 'Error while updating record.');
           //App::abort(500, 'Error');
         }
  }

  public function destroy($id)
  {

    //$products=Products::find($id);
    
    //if($products->delete())
    if ($this->productsRepository->delete($id))
    {
       return redirect()->route('products.listing')->with('success','User deleted successfully.');
    
    }else{
           return redirect()->route('products.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::products/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Products::with(['Categories', 'Units', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('sku', function($row){ $btn = $row->sku; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->categories)) { $btn = $row->categories->category_id; return $btn; } })
->addColumn('', function($row){ $btn = ''; if(isset($row->units)) { $btn = $row->units->unit_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('products.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('products.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('products.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }
}
