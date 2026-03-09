<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\SalesRepositoryInterface;
use Modules\Inventory\Entities\Sales;
use Modules\Inventory\Entities\Sale_Items;
use Modules\Inventory\Entities\Sale_Returns;
use Modules\Inventory\Entities\Sale_Return_Items;
use Modules\Inventory\Entities\Customers;
use Modules\Inventory\Entities\Products;
use Modules\Inventory\Entities\Inventory;
use Modules\Inventory\Entities\Stock_Movements;


use DB;
use DataTables;




class SalesController extends Controller
{
    protected $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        //if (!empty($keyword))
        //{

          //  $sales = Sales::with(['Customers', ])->get();
           
        //}else{
          //      $sales = Sales::with(['Customers', ])->paginate($perPage);
          //   }

		//$data['sales'] = $sales; 

        $data['sales'] = $this->salesRepository->getAll($perPage, $keyword);

        return view('inventory::sales/list', $data);
    }
    
    public function create()
    {
       $data = array();

       //$data['customers'] = Customers::orderBy('name')->pluck('name', 'id');
       //$data['products'] = Products::orderBy('name')->pluck('name', 'id');

       $customers = Customers::all();
       $products = Products::all();
	

       return view('inventory::sales/add', compact('customers', 'products'));
   }

   public function store(Request $request)
   {

     $data = array();

     $validated = $request->validate([
                                        'customer_id'           => 'required|exists:customers,id',
                                        'date'                  => 'required|date',
                                        'items'                 => 'required|array|min:1',
                                        'items.*.product_id'    => 'required|exists:products,id',
                                        'items.*.quantity'      => 'required|integer|min:1',
                                        'items.*.unit_price'    => 'required|numeric|min:0',
                                    ]);

        DB::beginTransaction();

        try {
                // Calculate total amount
                $totalAmount = collect($validated['items'])->sum(function($item) 
                {
                    return $item['quantity'] * $item['unit_price'];
                });

                // Create sale
                $sale = Sales::create([
                                            'customer_id'   => $validated['customer_id'],
                                            'date'          => $validated['date'],
                                            'total_amount'  => $totalAmount,
                                        ]);

                // Create sale items
                foreach ($validated['items'] as $item) 
                {
                    Sale_Items::create([
                                            'sale_id'       => $sale->id,
                                            'product_id'    => $item['product_id'],
                                            'quantity'      => $item['quantity'],
                                            'unit_price'    => $item['unit_price'],
                                            'subtotal'      => $item['quantity'] * $item['unit_price'],
                                        ]);

                    // Update inventory (optional)
                     Inventory::where('product_id', $item['product_id'])->decrement('quantity', $item['quantity']);
                }

                DB::commit();

                return redirect()->route('sales.listing', $sale->id)
                       ->with('success_message', 'Sale created successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error_message', 'Sale creation failed: ' . $e->getMessage());
            }


    
   }

   public function show(Request $request,$id)
   {
     $data = array();
     //$data['sales']=Sales::with(['Customers', ])->find($id);
     //$data['sales'] = $this->salesRepository->findById($id);
     $sale = Sales::with(['Customers', 'Items'])->findOrFail($id);
       
       return view('inventory::sales/show', compact('sale'));
  }

  public function edit($id)
  {
     $data = array();
	
     // $data['customers'] = Customers::orderBy('name')->pluck('name', 'id');
		

     // //$data['sales']= Sales::find($id);

     // $data['sales'] = $this->salesRepository->findById($id);

     $sale = Sales::with(['Items'])->findOrFail($id);
        $customers = Customers::all();
        $products = Products::all();
        //return view('inventory::sales.edit', compact('sale', 'customers', 'products'));


     return view('inventory::sales/edit', compact('sale', 'customers', 'products'));

  }

  public function update(Request $request,$id)
  {

    $data = array();

    $sale = Sales::with('items')->findOrFail($id);

        $validated = $request->validate([
                                            'customer_id'       => 'required|exists:customers,id',
                                            'date'              => 'required|date',
                                            'items'             => 'required|array|min:1',
                                            'items.*.product_id'=> 'required|exists:products,id',
                                            'items.*.quantity'  => 'required|integer|min:1',
                                            'items.*.unit_price'=> 'required|numeric|min:0',
                                        ]);

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = collect($validated['items'])->sum(function($item) 
            {
                return $item['quantity'] * $item['unit_price'];
            });

            // Update sale
            $sale->update([
                            'customer_id'   => $validated['customer_id'],
                            'date'          => $validated['date'],
                            'total_amount'  => $totalAmount,
                        ]);

            // Delete existing items
            $sale->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) 
            {
                Sale_Items::create([
                                        'sale_id'       => $sale->id,
                                        'product_id'    => $item['product_id'],
                                        'quantity'      => $item['quantity'],
                                        'unit_price'    => $item['unit_price'],
                                        'subtotal'      => $item['quantity'] * $item['unit_price'],
                                    ]);
            }

            DB::commit();

            return redirect()->route('sales.showing', $sale->id)
                   ->with('success_message', 'Sale updated successfully!');

        } catch (\Exception $e) 
        {
            DB::rollBack();
            return back()->with('error_message', 'Sale update failed: ' . $e->getMessage());
        }

    
  }

  public function destroy($id)
  {

    DB::beginTransaction();

        try {
            $sale = Sales::findOrFail($id);
            $sale->items()->delete();
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.listing')
                   ->with('success_message', 'Sale deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_message', 'Sale deletion failed: ' . $e->getMessage());
        }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::sales/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Sales::with(['Customers', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->customers)) { $btn = $row->customers->customer_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('sales.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('sales.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('sales.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }


 // Show return form
    public function showReturnForm($id)
    {
        $sale = Sales::with(['Customers', 'Items'])->findOrFail($id);
        return view('inventory::sales.return', compact('sale'));
    }

    // Process return
    public function processReturn(Request $request, $id)
    {
        $sale = Sales::with(['Items'])->findOrFail($id);

        $validated = $request->validate([
                                            'return_date'           => 'required|date',
                                            'reason'                => 'required|string|max:500',
                                            'items'                 => 'required|array|min:1',
                                            'items.*.id'            => 'required|exists:sale_items,id',
                                            'items.*.return_qty'    => 'required|integer|min:1',
                                        ]);

        DB::beginTransaction();

        try {
            // Create return record
            $return = Sale_Returns::create([
                                                'sale_id'       => $sale->id,
                                                'customer_id'   => $sale->customer_id,
                                                'return_date'   => $validated['return_date'],
                                                'total_amount'  => 0,
                                                'reason'        => $validated['reason'],
                                            ]);

            $totalAmount = 0;

            // Process return items
            foreach ($validated['items'] as $itemData) 
            {
                $saleItem   = $sale->items->find($itemData['id']);
                $returnQty  = $itemData['return_qty'];
                
                if ($returnQty > 0) 
                {
                    // Validate return quantity
                    if ($returnQty > $saleItem->quantity) 
                    {
                        throw new \Exception("Return quantity cannot exceed original sale quantity");
                    }

                    $subtotal = $saleItem->unit_price * $returnQty;
                    
                    // Create return item
                    Sale_Return_Items::create([
                                                'sale_return_id'    => $return->id,
                                                'product_id'        => $saleItem->product_id,
                                                'quantity'          => $returnQty,
                                                'unit_price'        => $saleItem->unit_price,
                                                'subtotal'          => $subtotal,
                                            ]);

                    $totalAmount += $subtotal;

                    // Record stock movement
                    Stock_Movements::create([
                                                'product_id'    => $saleItem->product_id,
                                                'type'          => 'IN',
                                                'quantity'      => $returnQty,
                                                'reason'        => 'Sale Return',
                                                'reference_id'  => $return->id,
                                            ]);

                    // Update inventory (optional)
                     Inventory::where('product_id', $saleItem->product_id)->increment('quantity', $returnQty);
                }
            }

            // Update return total amount
            $return->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('sales.showing', $sale->id)
                   ->with('success_message', 'Return processed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_message', 'Return processing failed: ' . $e->getMessage());
        }
    }


}
