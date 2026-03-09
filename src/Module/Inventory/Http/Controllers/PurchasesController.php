<?php
namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Modules\Inventory\Repositories\Interfaces\PurchasesRepositoryInterface;
use Modules\Inventory\Entities\Purchases;
use Modules\Inventory\Entities\Suppliers;
use Modules\Inventory\Entities\Purchase_Items;
use Modules\Inventory\Entities\Products;
use Modules\Inventory\Entities\Stock_Movements;
use Modules\Inventory\Entities\Inventory;
use Modules\Inventory\Entities\Purchase_Returns;
use Modules\Inventory\Entities\Purchase_Return_Items;



use DB;
use DataTables;

class PurchasesController extends Controller
{
    protected $purchasesRepository;

    public function __construct(PurchasesRepositoryInterface $purchasesRepository)
    {
        $this->purchasesRepository = $purchasesRepository;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 30;
        $data    = array();

        $data["title"] = "Purchases";

        //if (!empty($keyword))
        //{

          //  $purchases = Purchases::with(['Suppliers', ])->get();
           
        //}else{
          //      $purchases = Purchases::with(['Suppliers', ])->paginate($perPage);
          //   }

		//$data['purchases'] = $purchases; 

        $data['purchases'] = $this->purchasesRepository->getAll($perPage, $keyword);

        return view('inventory::purchases/list', $data);
    }
    
    public function create()
    {
       $data = array();

       $data['suppliers'] = Suppliers::orderBy('name')->pluck('name', 'id');
       $data['products'] = Products::orderBy('name')->pluck('name', 'id');
	

       return view('inventory::purchases/add', $data);
   }

   public function store(Request $request)
   {

      $data = array();

     $validatedData = $request->validate([
                                                'form_supplier_id'      => 'required|integer|exists:suppliers,id',
                                                'form_purchase_date'    => 'required|date',
                                                'form_invoice_no'       => 'required|string|max:255',
                                                'form_total_amount'     => 'required|numeric|min:0',
                                                'form_product_id'       => 'required|array|min:1',
                                                'form_product_id.*'     => 'required|integer|exists:products,id',
                                                'form_quantity'         => 'required|array|min:1',
                                                'form_quantity.*'       => 'required|integer|min:1',
                                                'form_unit_price'       => 'required|array|min:1',
                                                'form_unit_price.*'     => 'required|numeric|min:0',
                                                //'form_price'            => 'required|array|min:1',
                                                //'form_price.*'          => 'required|numeric|min:0',
                                            ]);

    // Calculate items total
    $itemsTotal = array_sum($request->form_price);
    
    // Validate that items total doesn't exceed the total amount
    if ($itemsTotal > $request->form_total_amount) 
    {
        return back()->withErrors(['error_message' => 'The sum of purchase items exceeds the total amount.'])->withInput();
    }
//dd($itemsTotal , $request->form_total_amount);
    // Start database transaction
    DB::beginTransaction();

    try {
            $poData = [
                                            'supplier_id'   => $validatedData['form_supplier_id'],
                                            'purchase_date' => date("Y-m-d", strtotime($validatedData['form_purchase_date'])),
                                            'invoice_no'    => $validatedData['form_invoice_no'],
                                            'total_amount'  => $validatedData['form_total_amount'],
                                        ];

            //dd($poData);

            // Create purchase record
            $purchase = Purchases::create($poData); //dd($purchase);

        // Create purchase items
        foreach ($validatedData['form_product_id'] as $key => $productId) 
        {
            $ItemsData = [
                                    'purchase_id'   => $purchase->id,
                                    'product_id'    => $productId,
                                    'quantity'      => $validatedData['form_quantity'][$key],
                                    'unit_price'    => $validatedData['form_unit_price'][$key],
                                    'total_amount'  => ($validatedData['form_quantity'][$key]*$validatedData['form_unit_price'][$key]),
                                    'is_paid'       => false, // Default to not paid
                                ];
            //dd($ItemsData);

            Purchase_Items::create($ItemsData);

            //inventory
            Inventory::updateOrCreate(
                                        [
                                            'product_id' => $purchase->id,
                                            //'warehouse_id' => $purchase->warehouse_id,
                                            'quantity' => DB::raw("quantity + {$validatedData['form_quantity'][$key]}")
                                        ]
                                        );

            //manage stocks
            $quantity = $validatedData['form_quantity'][$key];
            // Record stock movement (IN)
            Stock_Movements::create([
                'product_id'   => $productId,
                //'warehouse_id' => $validatedData['form_warehouse_id'],
                'type'         => 'IN', // IN for purchase/stock addition
                'quantity'     => $quantity,
                'reason'       => 'Purchase', // or 'Purchase Order' etc.
                'reference_id' => $purchase->id,
                'created_at'   => now(),
            ]);


            // Auto Vouching - Create accounting voucher for the purchase
            // here $voucherType = 1 means journel voucher
            createPurchaseVoucher($purchase, $voucherType = 1);

        }

        DB::commit();

        return redirect()->route('purchases.listing')->with('success_message', 'Purchase recorded successfully!');
    } catch (\Exception $e) 
    {
        //dd($e->getMessage());
        DB::rollBack();
        return back()->with('error_message', 'Failed to save purchase: ' . $e->getMessage())->withInput();

    }



   }

   public function show(Request $request, $id)
{
    $purchase = Purchases::with(['Suppliers', 'items'])->findOrFail($id);
    
    return view('inventory::purchases.show', [
        'purchase' => $purchase,
        'items' => $purchase->items,
        'is_approved' => 0
    ]);
}

  public function edit($id)
  {
     // $data = array();
	
     // $data['suppliers'] = Suppliers::orderBy('name')->pluck('name', 'id');
		

     // //$data['purchases']= Purchases::find($id);

     // $data['purchases'] = $this->purchasesRepository->findById($id);

     // return view('inventory::purchases/edit', $data);
    $purchase = Purchases::with('items')->findOrFail($id);
    $suppliers = Suppliers::pluck('name', 'id');
    $products = Products::pluck('name', 'id');
    
    return view('inventory::purchases/edit', compact('purchase', 'suppliers', 'products'));

  }

  public function update(Request $request,$id)
  {

    $data = array();

        $validatedData = $request->validate([
            'form_supplier_id'      => 'required|integer|exists:suppliers,id',
            'form_purchase_date'    => 'required|date',
            'form_invoice_no'       => 'required|string|max:255',
            'form_total_amount'     => 'required|numeric|min:0',
            'form_product_id'       => 'required|array|min:1',
            'form_product_id.*'     => 'required|integer|exists:products,id',
            'form_quantity'         => 'required|array|min:1',
            'form_quantity.*'       => 'required|integer|min:1',
            'form_unit_price'       => 'required|array|min:1',
            'form_unit_price.*'     => 'required|numeric|min:0',
            //'form_price'            => 'required|array|min:1',
            //'form_price.*'          => 'required|numeric|min:0',
        ]);

        // Calculate items total
        $itemsTotal = array_sum($request->form_price);
        
        // Validate that items total doesn't exceed the total amount
        if ($itemsTotal > $request->form_total_amount) 
        {
            return back()->withErrors(['error_message' => 'The sum of purchase items exceeds the total amount.'])->withInput();
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Update purchase record
            $purchase = Purchases::findOrFail($id);

            // 1. First reverse all existing stock movements
            foreach ($purchase->items as $item) 
            {
                // Create reversal entry (OUT)
                Stock_Movements::create([
                    'product_id'   => $item->product_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'type'        => 'OUT',
                    'quantity'     => $item->quantity,
                    'reason'       => 'Purchase Adjustment (Reversal)',
                    'reference_id' => $purchase->id,
                    'created_at'   => now(),
                ]);
                
                // Optional: Update product stock (if you maintain a stock table)
                // ProductStock::where('product_id', $item->product_id)
                //     ->where('warehouse_id', $purchase->warehouse_id)
                //     ->decrement('quantity', $item->quantity);
            }

            $purchase->update([
                                'supplier_id'   => $validatedData['form_supplier_id'],
                                'purchase_date' => $validatedData['form_purchase_date'],
                                'invoice_no'    => $validatedData['form_invoice_no'],
                                'total_amount'  => $validatedData['form_total_amount'],
                            ]);

            // First, delete all existing items
            $purchase->items()->delete();

            // Create new purchase items
            foreach ($validatedData['form_product_id'] as $key => $productId) 
            {
                Purchase_Items::create([
                    'purchase_id'   => $purchase->id,
                    'product_id'    => $productId,
                    'quantity'      => $validatedData['form_quantity'][$key],
                    'unit_price'    => $validatedData['form_unit_price'][$key],
                    'total_amount'  => ($validatedData['form_quantity'][$key]*$validatedData['form_unit_price'][$key]),
                    //'is_paid'       => false, // You might want to preserve this from original
                ]);


                // Record new stock movement (IN)
                Stock_Movements::create([
                    'product_id'   => $productId,
                    'warehouse_id' => $validatedData['form_warehouse_id'],
                    'type'        => 'IN',
                    'quantity'    => $quantity,
                    'reason'      => 'Purchase Update',
                    'reference_id' => $purchase->id,
                    'created_at'  => now(),
                ]);

            }


            // Auto Vouching - Update accounting voucher for the purchase
            $this->updatePurchaseVoucher($purchase, $itemsTotal);

            DB::commit();

            return redirect()->route('purchases.listing')->with('success_message', 'Purchase updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_message', 'Failed to update purchase: ' . $e->getMessage())->withInput();
        }
  }

  public function approvePurchase(Request $request, $id)
{
    // Verify the user has permission to approve purchases
    if (!auth()->user()->can('approve_purchases')) {
        abort(403, 'Unauthorized action.');
    }

    DB::beginTransaction();

    try {
        $purchase = Purchases::with('items')->where('is_approved', 0)->findOrFail($id);

        // Update purchase status
        $purchase->update([
            'is_approved' => 1,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        

        // Record stock movements only when approved
        foreach ($purchase->items as $item) 
        {

            //Inventory
        Inventory::updateOrCreate(
                [
                    'product_id' => $item->product_id,
                   // 'warehouse_id' => $purchase->warehouse_id,
                
                    'quantity' => DB::raw("COALESCE(quantity, 0) + {$item->quantity}")
                ]
            );
        
            StockMovement::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $purchase->warehouse_id,
                'type' => 'IN',
                'quantity' => $item->quantity,
                'reason' => 'Purchase Approval',
                'reference_id' => $purchase->id,
            ]);

            // Optional: Update product stock quantity
            // ProductStock::updateOrCreate(
            //     ['product_id' => $item->product_id, 'warehouse_id' => $purchase->warehouse_id],
            //     ['quantity' => DB::raw("quantity + {$item->quantity}")]
            // );
        }

        DB::commit();

        return redirect()->route('purchases.listing')
               ->with('success_message', 'Purchase #'.$purchase->invoice_no.' approved successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error_message', 'Failed to approve purchase: ' . $e->getMessage());
    }
}

  public function destroy($id)
  {

    //$purchases=Purchases::find($id);
    
    //if($purchases->delete())
    if ($this->purchasesRepository->delete($id))
    {
       return redirect()->route('purchases.listing')->with('success_message','User deleted successfully.');
    
    }else{
           return redirect()->route('purchases.listing')->with('error_message', 'Error while deleting record.');
           //App::abort(500, 'Error');
         }
  }

#yajra
 public function yajra_index(Request $request)
    {
        
       $data = array();
        return view('inventory::purchases/yajralisting', $data);
    }

    public function yajra_data(Request $request)
    {

        if ($request->ajax()) {
            
            
            $data = Purchases::with(['Suppliers', ]);
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){ $btn = $row->id; return $btn; })
->addColumn('', function($row){ $btn = ''; if(isset($row->suppliers)) { $btn = $row->suppliers->supplier_id; return $btn; } })

                   ->addColumn('action', function($item){

$editlink = "<a href='".route('purchases.editing', ['id' => $item->id])."' title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>";
$showlink = "<a href='".route('purchases.showing', ['id' => $item->id])."' title='Show'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>";
$deleelink = "<a href='".route('purchases.deleting', ['id' => $item->id])."' title='Delete'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>";


$btn = ''.$editlink.''.$showlink.''.$deleelink;
                            return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);

        }

 }


 //return items
 public function showReturnForm($id)
{
   //dd('sani', $id);
    try
    {


    $purchase = Purchases::with(['items', 'Suppliers'])
        ->where('is_approved', 0)
        ->findOrFail($id);

        //dd($purchase);
    }catch (\Exception $e)
    {
        dd($e->getMessage());
    }

    //return view('inventory::purchases.return', compact('purchase'));
    return view('inventory::purchases/returning', compact('purchase'));


}

public function processReturn(Request $request, $id)
{
    $purchase = Purchases::with('items')->findOrFail($id);

    $validated = $request->validate([
        'return_date' => 'required|date',
        'reason' => 'required|string|max:500',
        'items' => 'required|array|min:1',
        'items.*.id' => 'required|exists:purchase_items,id',
        'items.*.return_qty' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        // Create purchase return
        $return = Purchase_Returns::create([
            'purchase_id' => $purchase->id,
            'supplier_id' => $purchase->supplier_id,
            'return_date' => $validated['return_date'],
            'total_amount' => 0, // Will be calculated
            'reason' => $validated['reason'],
            'created_by' => auth()->id(),
        ]);

        $totalAmount = 0;

        // Process return items
        foreach ($validated['items'] as $itemData) {
            $purchaseItem = $purchase->items->find($itemData['id']);
            
            if ($itemData['return_qty'] > 0) {
                $subtotal = $purchaseItem->unit_price * $itemData['return_qty'];
                
                Purchase_Return_Items::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $purchaseItem->product_id,
                    'unit_price' => $purchaseItem->unit_price,
                    'quantity' => $itemData['return_qty'],
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;

                
                //inventory
            Inventory::updateOrCreate(
                                        [
                                            'product_id' => $purchaseItem->product_id,
                                            //'warehouse_id' => $purchase->warehouse_id,
                                            'quantity' => DB::raw("quantity + {$itemData['return_qty']}")
                                        ]
                                        );

                // Record stock movement (OUT)
                Stock_Movements::create([
                    'product_id' => $purchaseItem->product_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'type' => 'OUT',
                    'quantity' => $itemData['return_qty'],
                    'reason' => 'Purchase Return',
                    'reference_id' => $return->id,
                ]);

                // Optional: Update product stock
                // ProductStock::where('product_id', $purchaseItem->product_id)
                //     ->where('warehouse_id', $purchase->warehouse_id)
                //     ->decrement('quantity', $itemData['return_qty']);
            }
        }

        // Update return total amount
        $return->update(['total_amount' => $totalAmount]);

        DB::commit();

        return redirect()->route('purchases.listing', $purchase->id)
               ->with('success_message', 'Return processed successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        //return back()->with('error', 'Failed to process return: ' . $e->getMessage());
        return redirect()->route('purchases.listing', $purchase->id)->with('error_message','unable to return. '.$e->getMessage());
    }
}

}
