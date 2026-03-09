<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;

class InventoryReportsController extends Controller
{
    //Current Stock Status
    public function CurrentStockStatus(Request $request)
    {
        $data    = array();
        // Get filter parameters from request
        $warehouse_id = $request->input('warehouse_id');
        $category_id = $request->input('category_id');
        $search_term = $request->input('search');
        
        // Base query
        $query = DB::table('inventory as i')
            ->join('products as p', 'i.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('warehouses as w', 'i.warehouse_id', '=', 'w.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'p.barcode',
                'p.cost_price',
                'p.sale_price',
                'c.name as category_name',
                'w.name as warehouse_name',
                'i.quantity',
                'u.name as unit_name',
                DB::raw('(i.quantity * p.cost_price) as inventory_cost_value'),
                DB::raw('(i.quantity * p.sale_price) as inventory_sale_value')
            )
            ->orderBy('p.name');
        
        // Apply filters
        if ($warehouse_id) {
            $query->where('i.warehouse_id', $warehouse_id);
        }
        
        if ($category_id) {
            $query->where('p.category_id', $category_id);
        }
        
        if ($search_term) {
            $query->where(function($q) use ($search_term) {
                $q->where('p.name', 'like', "%$search_term%")
                  ->orWhere('p.sku', 'like', "%$search_term%")
                  ->orWhere('p.barcode', 'like', "%$search_term%");
            });
        }
        
        $inventory = $query->get();
        
        // Get data for filters
        $warehouses = \DB::table('warehouses')->get();
        $categories = \DB::table('categories')->get();
        
        // Calculate totals
        $total_cost_value = $inventory->sum('inventory_cost_value');
        $total_sale_value = $inventory->sum('inventory_sale_value');
        
        $data = [
                    'inventory'         => $inventory,
                    'warehouses'        => $warehouses,
                    'categories'        => $categories,
                    'total_cost_value'  => $total_cost_value,
                    'total_sale_value'  => $total_sale_value,
                    'filters'           => [
                                                'warehouse_id'  => $warehouse_id,
                                                'category_id'   => $category_id,
                                                'search'        => $search_term
                                            ]
                ];

        return view('inventory::Reports/CurrentStockStatus', $data);
    }

    //Low Stock Alert
    public function LowStockAlert(Request $request)
    {
        // Get filter parameters from request
        $warehouse_id   = $request->input('warehouse_id');
        $category_id    = $request->input('category_id');
        $severity       = $request->input('severity', 'critical'); // critical, warning, all
        
        // Base query - products where quantity < minimum_quantity
        $query = \DB::table('inventory as i')
            ->join('products as p', 'i.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->join('warehouses as w', 'i.warehouse_id', '=', 'w.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'p.barcode',
                'p.cost_price',
                'p.sale_price',
                'p.minimum_quantity',
                'p.reorder_quantity',
                'c.name as category_name',
                'w.name as warehouse_name',
                'w.id as warehouse_id',
                'i.quantity',
                'u.name as unit_name',
                \DB::raw('(p.minimum_quantity - i.quantity) as shortage'),
                \DB::raw('(i.quantity / p.minimum_quantity * 100) as stock_level_percent'),
                \DB::raw('CASE 
                    WHEN i.quantity <= (p.minimum_quantity * 0.5) THEN "critical"
                    WHEN i.quantity < p.minimum_quantity THEN "warning"
                    ELSE "adequate"
                END as stock_status')
            )
            ->where('i.quantity', '<', \DB::raw('p.minimum_quantity'))
            ->orderBy('stock_status', 'desc')
            ->orderBy('shortage', 'desc');
        
        // Apply filters
        if ($warehouse_id) 
        {
            $query->where('i.warehouse_id', $warehouse_id);
        }
        
        if ($category_id) 
        {
            $query->where('p.category_id', $category_id);
        }
        
        if ($severity !== 'all') 
        {
            $query->where(function($q) use ($severity) 
            {
                if ($severity === 'critical') 
                {
                    $q->where('i.quantity', '<=', \DB::raw('p.minimum_quantity * 0.5'));
                } elseif ($severity === 'warning') 
                        {
                            $q->where('i.quantity', '>', \DB::raw('p.minimum_quantity * 0.5'))
                              ->where('i.quantity', '<', \DB::raw('p.minimum_quantity'));
                        }
            });
        }
        
        $low_stock_items = $query->get();
        
        // Get data for filters
        $warehouses = \DB::table('warehouses')->get();
        $categories = \DB::table('categories')->get();
        
        return view('inventory::Reports/LowStockAlert', [
                                                'low_stock_items'   => $low_stock_items,
                                                'warehouses'        => $warehouses,
                                                'categories'        => $categories,
                                                'filters'           => [
                                                                            'warehouse_id' => $warehouse_id,
                                                                            'category_id' => $category_id,
                                                                            'severity' => $severity
                                                                        ]
                                            ]);
    }

    //Sales Summary
    public function SalesSummary(Request $request)
    {
        // Get filter parameters from request
        $date_range = $request->input('date_range', 'this_month');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $customer_id = $request->input('customer_id');
        $group_by = $request->input('group_by', 'day'); // day, week, month, year, product, customer
        
        // Determine date range
        switch ($date_range) {
            case 'today':
                $start_date = now()->format('Y-m-d');
                $end_date = now()->format('Y-m-d');
                break;
            case 'yesterday':
                $start_date = now()->subDay()->format('Y-m-d');
                $end_date = now()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $start_date = now()->startOfWeek()->format('Y-m-d');
                $end_date = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d');
                $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $start_date = now()->startOfMonth()->format('Y-m-d');
                $end_date = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $start_date = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $end_date = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $start_date = now()->startOfYear()->format('Y-m-d');
                $end_date = now()->endOfYear()->format('Y-m-d');
                break;
            case 'last_year':
                $start_date = now()->subYear()->startOfYear()->format('Y-m-d');
                $end_date = now()->subYear()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Use the provided dates
                break;
            default:
                $start_date = now()->startOfMonth()->format('Y-m-d');
                $end_date = now()->endOfMonth()->format('Y-m-d');
        }

        // Base query
        $query = \DB::table('sales as s')
            ->join('customers as c', 's.customer_id', '=', 'c.id')
            ->join('sale_items as si', 's.id', '=', 'si.sale_id')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->whereBetween('s.date', [$start_date, $end_date])
            ->where('s.total_amount', '>', 0); // Exclude returns/voids

        // Apply customer filter
        if ($customer_id) {
            $query->where('s.customer_id', $customer_id);
        }

        // Select fields based on grouping
        switch ($group_by) {
            case 'product':
                $query->select(
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.sku',
                    \DB::raw('SUM(si.quantity) as total_quantity'),
                    \DB::raw('SUM(si.subtotal) as total_amount'),
                    \DB::raw('COUNT(DISTINCT s.id) as order_count')
                )
                ->groupBy('p.id', 'p.name', 'p.sku')
                ->orderBy('total_amount', 'desc');
                break;
                
            case 'customer':
                $query->select(
                    'c.id as customer_id',
                    'c.name as customer_name',
                    \DB::raw('SUM(si.subtotal) as total_amount'),
                    \DB::raw('COUNT(DISTINCT s.id) as order_count'),
                    \DB::raw('SUM(si.quantity) as total_quantity')
                )
                ->groupBy('c.id', 'c.name')
                ->orderBy('total_amount', 'desc');
                break;
                
            case 'week':
                $query->select(
                    \DB::raw('YEAR(s.date) as year'),
                    \DB::raw('WEEK(s.date) as week'),
                    \DB::raw('MIN(s.date) as week_start'),
                    \DB::raw('MAX(s.date) as week_end'),
                    \DB::raw('SUM(si.subtotal) as total_amount'),
                    \DB::raw('COUNT(DISTINCT s.id) as order_count'),
                    \DB::raw('SUM(si.quantity) as total_quantity')
                )
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc');
                break;
                
            case 'month':
                $query->select(
                    \DB::raw('YEAR(s.date) as year'),
                    \DB::raw('MONTH(s.date) as month'),
                    \DB::raw('DATE_FORMAT(s.date, "%Y-%m") as month_name'),
                    \DB::raw('SUM(si.subtotal) as total_amount'),
                    \DB::raw('COUNT(DISTINCT s.id) as order_count'),
                    \DB::raw('SUM(si.quantity) as total_quantity')
                )
                ->groupBy('year', 'month', 'month_name')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc');
                break;
                
            case 'year':
                $query->select(
                    \DB::raw('YEAR(s.date) as year'),
                    \DB::raw('SUM(si.subtotal) as total_amount'),
                    \DB::raw('COUNT(DISTINCT s.id) as order_count'),
                    \DB::raw('SUM(si.quantity) as total_quantity')
                )
                ->groupBy('year')
                ->orderBy('year', 'desc');
                break;
                
            default: // day
                $query->select(
                    's.date',
                    \DB::raw('SUM(si.subtotal) as total_amount'),
                    \DB::raw('COUNT(DISTINCT s.id) as order_count'),
                    \DB::raw('SUM(si.quantity) as total_quantity')
                )
                ->groupBy('s.date')
                ->orderBy('s.date', 'desc');
        }

        $sales_data = $query->get();

        // Calculate summary totals
        $summary_totals = [
            'total_amount' => $sales_data->sum('total_amount'),
            'order_count' => $sales_data->sum('order_count'),
            'total_quantity' => $sales_data->sum('total_quantity'),
            'avg_order_value' => $sales_data->sum('order_count') > 0 
                ? $sales_data->sum('total_amount') / $sales_data->sum('order_count') 
                : 0
        ];

        // Get data for filters
        $customers = \DB::table('customers')->orderBy('name')->get();

        return view('inventory::Reports/SalesSummary', [
            'sales_data' => $sales_data,
            'summary_totals' => $summary_totals,
            'customers' => $customers,
            'filters' => [
                'date_range' => $date_range,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'customer_id' => $customer_id,
                'group_by' => $group_by
            ]
        ]);
    }

    //Customer Purchase History
    public function CustomerPurchaseHistory(Request $request)
    {
        // Get filter parameters from request
        $customer_id    = $request->input('customer_id');
        $date_range     = $request->input('date_range', 'this_month');
        $start_date     = $request->input('start_date');
        $end_date       = $request->input('end_date');
        $sort_by        = $request->input('sort_by', 'recent'); // recent, highest, frequent

        // Determine date range
        switch ($date_range) 
        {
            case 'today':
                $start_date = now()->format('Y-m-d');
                $end_date = now()->format('Y-m-d');
                break;
            case 'yesterday':
                $start_date = now()->subDay()->format('Y-m-d');
                $end_date = now()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $start_date = now()->startOfWeek()->format('Y-m-d');
                $end_date = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d');
                $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $start_date = now()->startOfMonth()->format('Y-m-d');
                $end_date = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $start_date = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $end_date = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $start_date = now()->startOfYear()->format('Y-m-d');
                $end_date = now()->endOfYear()->format('Y-m-d');
                break;
            case 'last_year':
                $start_date = now()->subYear()->startOfYear()->format('Y-m-d');
                $end_date = now()->subYear()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Use the provided dates
                break;
            default:
                $start_date = now()->subYear()->startOfYear()->format('Y-m-d');
                $end_date = now()->endOfYear()->format('Y-m-d');
        }

        // Base query for customer summary
        $customer_query = \DB::table('customers as c')
            ->leftJoin('sales as s', 'c.id', '=', 's.customer_id')
            ->leftJoin('sale_items as si', 's.id', '=', 'si.sale_id')
            ->select(
                'c.id',
                'c.name',
                'c.phone',
                'c.email',
                \DB::raw('COUNT(DISTINCT s.id) as purchase_count'),
                \DB::raw('SUM(si.subtotal) as total_spent'),
                \DB::raw('MAX(s.date) as last_purchase_date'),
                \DB::raw('SUM(si.quantity) as total_items_purchased'),
                \DB::raw('AVG(si.subtotal) as avg_order_value')
            )
            ->whereBetween('s.date', [$start_date, $end_date])
            ->groupBy('c.id', 'c.name', 'c.phone', 'c.email');

        // Apply customer filter if specified
        if ($customer_id) 
        {
            $customer_query->where('c.id', $customer_id);
        }

        // Apply sorting
        switch ($sort_by) {
            case 'highest':
                $customer_query->orderBy('total_spent', 'desc');
                break;
            case 'frequent':
                $customer_query->orderBy('purchase_count', 'desc');
                break;
            default: // recent
                $customer_query->orderBy('last_purchase_date', 'desc');
        }

        $customers = $customer_query->get();

        // Get detailed purchase history for each customer
        $detailed_history = [];
        foreach ($customers as $customer) 
        {
            $history_query = \DB::table('sales as s')
                ->join('sale_items as si', 's.id', '=', 'si.sale_id')
                ->join('products as p', 'si.product_id', '=', 'p.id')
                ->select(
                    's.id as sale_id',
                    's.date',
                    's.total_amount',
                    'p.name as product_name',
                    'p.sku',
                    'si.quantity',
                    'si.unit_price',
                    'si.subtotal'
                )
                ->where('s.customer_id', $customer->id)
                ->whereBetween('s.date', [$start_date, $end_date])
                ->orderBy('s.date', 'desc');

            $detailed_history[$customer->id] = $history_query->get();
        }

        // Get all customers for filter dropdown
        $all_customers = \DB::table('customers')->orderBy('name')->get();

        return view('inventory::Reports/CustomerPurchaseHistory', [
                                                        'customers'         => $customers,
                                                        'detailed_history'  => $detailed_history,
                                                        'all_customers'     => $all_customers,
                                                        'filters'           => [
                                                                                    'customer_id'   => $customer_id,
                                                                                    'date_range'    => $date_range,
                                                                                    'start_date'    => $start_date,
                                                                                    'end_date'      => $end_date,
                                                                                    'sort_by'       => $sort_by
                                                                                ],
                                                        'summary_totals'    => [
                                                                                    'total_customers'   => $customers->count(),
                                                                                    'total_sales'       => $customers->sum('total_spent'),
                                                                                    'total_orders'      => $customers->sum('purchase_count'),
                                                                                    'avg_order_value'   => $customers->avg('avg_order_value')
                                                                                ]
                                                    ]);
    }

    //Purchase Summary
    public function PurchaseSummary(Request $request)
    {
        // Get filter parameters from request
        $date_range = $request->input('date_range', 'this_month');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $supplier_id = $request->input('supplier_id');
        $group_by = $request->input('group_by', 'day'); // day, week, month, year, supplier, product
        $status = $request->input('status', 'all'); // all, paid, unpaid

        // Determine date range
        switch ($date_range) {
            case 'today':
                $start_date = now()->format('Y-m-d');
                $end_date = now()->format('Y-m-d');
                break;
            case 'yesterday':
                $start_date = now()->subDay()->format('Y-m-d');
                $end_date = now()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $start_date = now()->startOfWeek()->format('Y-m-d');
                $end_date = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d');
                $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $start_date = now()->startOfMonth()->format('Y-m-d');
                $end_date = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $start_date = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $end_date = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $start_date = now()->startOfYear()->format('Y-m-d');
                $end_date = now()->endOfYear()->format('Y-m-d');
                break;
            case 'last_year':
                $start_date = now()->subYear()->startOfYear()->format('Y-m-d');
                $end_date = now()->subYear()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Use the provided dates
                break;
            default:
                $start_date = now()->subYear()->startOfYear()->format('Y-m-d');
                $end_date = now()->endOfYear()->format('Y-m-d');
        }

        // Base query
        $query = \DB::table('purchases as p')
            ->join('suppliers as s', 'p.supplier_id', '=', 's.id')
            ->join('purchase_items as pi', 'p.id', '=', 'pi.purchase_id')
            ->join('products as pr', 'pi.product_id', '=', 'pr.id')
            ->whereBetween('p.purchase_date', [$start_date, $end_date]);

        // Apply supplier filter
        if ($supplier_id) {
            $query->where('p.supplier_id', $supplier_id);
        }

        // Apply payment status filter
        if ($status !== 'all') {
            $query->where('pi.is_paid', $status === 'paid' ? 1 : 0);
        }

        // Select fields based on grouping
        switch ($group_by) {
            case 'product':
                $query->select(
                    'pr.id as product_id',
                    'pr.name as product_name',
                    'pr.sku',
                    \DB::raw('SUM(pi.quantity) as total_quantity'),
                    \DB::raw('SUM(pi.total_amount) as total_amount'),
                    \DB::raw('COUNT(DISTINCT p.id) as purchase_count'),
                    \DB::raw('AVG(pi.unit_price) as avg_unit_price')
                )
                ->groupBy('pr.id', 'pr.name', 'pr.sku')
                ->orderBy('total_amount', 'desc');
                break;
                
            case 'supplier':
                $query->select(
                    's.id as supplier_id',
                    's.name as supplier_name',
                    \DB::raw('SUM(pi.total_amount) as total_amount'),
                    \DB::raw('COUNT(DISTINCT p.id) as purchase_count'),
                    \DB::raw('SUM(pi.quantity) as total_quantity'),
                    \DB::raw('AVG(pi.unit_price) as avg_unit_price')
                )
                ->groupBy('s.id', 's.name')
                ->orderBy('total_amount', 'desc');
                break;
                
            case 'week':
                $query->select(
                    \DB::raw('YEAR(p.purchase_date) as year'),
                    \DB::raw('WEEK(p.purchase_date) as week'),
                    \DB::raw('MIN(p.purchase_date) as week_start'),
                    \DB::raw('MAX(p.purchase_date) as week_end'),
                    \DB::raw('SUM(pi.total_amount) as total_amount'),
                    \DB::raw('COUNT(DISTINCT p.id) as purchase_count'),
                    \DB::raw('SUM(pi.quantity) as total_quantity')
                )
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc');
                break;
                
            case 'month':
                $query->select(
                    \DB::raw('YEAR(p.purchase_date) as year'),
                    \DB::raw('MONTH(p.purchase_date) as month'),
                    \DB::raw('DATE_FORMAT(p.purchase_date, "%Y-%m") as month_name'),
                    \DB::raw('SUM(pi.total_amount) as total_amount'),
                    \DB::raw('COUNT(DISTINCT p.id) as purchase_count'),
                    \DB::raw('SUM(pi.quantity) as total_quantity')
                )
                ->groupBy('year', 'month', 'month_name')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc');
                break;
                
            case 'year':
                $query->select(
                    \DB::raw('YEAR(p.purchase_date) as year'),
                    \DB::raw('SUM(pi.total_amount) as total_amount'),
                    \DB::raw('COUNT(DISTINCT p.id) as purchase_count'),
                    \DB::raw('SUM(pi.quantity) as total_quantity')
                )
                ->groupBy('year')
                ->orderBy('year', 'desc');
                break;
                
            default: // day
                $query->select(
                    'p.purchase_date as date',
                    \DB::raw('SUM(pi.total_amount) as total_amount'),
                    \DB::raw('COUNT(DISTINCT p.id) as purchase_count'),
                    \DB::raw('SUM(pi.quantity) as total_quantity')
                )
                ->groupBy('p.purchase_date')
                ->orderBy('p.purchase_date', 'desc');
        }

        $purchase_data = $query->get();

        // Calculate summary totals
        $summary_totals = [
            'total_amount' => $purchase_data->sum('total_amount'),
            'purchase_count' => $purchase_data->sum('purchase_count'),
            'total_quantity' => $purchase_data->sum('total_quantity'),
            'avg_purchase_value' => $purchase_data->sum('purchase_count') > 0 
                ? $purchase_data->sum('total_amount') / $purchase_data->sum('purchase_count') 
                : 0
        ];

        // Get data for filters
        $suppliers = \DB::table('suppliers')->orderBy('name')->get();

        return view('inventory::Reports/PurchaseSummary', [
            'purchase_data' => $purchase_data,
            'summary_totals' => $summary_totals,
            'suppliers' => $suppliers,
            'filters' => [
                'date_range' => $date_range,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'supplier_id' => $supplier_id,
                'group_by' => $group_by,
                'status' => $status
            ]
        ]);
    }

    //Profit & Loss
    public function ProfitNLoss(Request $request)
{
    // Get filter parameters from request
    $date_range = $request->input('date_range', 'this_month');
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');
    $compare_with = $request->input('compare_with', 'none'); // none, previous_period, previous_year

    // Determine date range
    switch ($date_range) {
        case 'today':
            $start_date = now()->format('Y-m-d');
            $end_date = now()->format('Y-m-d');
            break;
        case 'yesterday':
            $start_date = now()->subDay()->format('Y-m-d');
            $end_date = now()->subDay()->format('Y-m-d');
            break;
        case 'this_week':
            $start_date = now()->startOfWeek()->format('Y-m-d');
            $end_date = now()->endOfWeek()->format('Y-m-d');
            break;
        case 'last_week':
            $start_date = now()->subWeek()->startOfWeek()->format('Y-m-d');
            $end_date = now()->subWeek()->endOfWeek()->format('Y-m-d');
            break;
        case 'this_month':
            $start_date = now()->startOfMonth()->format('Y-m-d');
            $end_date = now()->endOfMonth()->format('Y-m-d');
            break;
        case 'last_month':
            $start_date = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $end_date = now()->subMonth()->endOfMonth()->format('Y-m-d');
            break;
        case 'this_quarter':
            $start_date = now()->startOfQuarter()->format('Y-m-d');
            $end_date = now()->endOfQuarter()->format('Y-m-d');
            break;
        case 'last_quarter':
            $start_date = now()->subQuarter()->startOfQuarter()->format('Y-m-d');
            $end_date = now()->subQuarter()->endOfQuarter()->format('Y-m-d');
            break;
        case 'this_year':
            $start_date = now()->startOfYear()->format('Y-m-d');
            $end_date = now()->endOfYear()->format('Y-m-d');
            break;
        case 'last_year':
            $start_date = now()->subYear()->startOfYear()->format('Y-m-d');
            $end_date = now()->subYear()->endOfYear()->format('Y-m-d');
            break;
        case 'custom':
            // Use the provided dates
            break;
        default:
            $start_date = now()->startOfMonth()->format('Y-m-d');
            $end_date = now()->endOfMonth()->format('Y-m-d');
    }

    // Calculate comparison dates if needed
    $comparison_data = null;
    $comparison_start = null;
    $comparison_end = null;

    if ($compare_with !== 'none') {
        if ($compare_with === 'previous_period') {
            $days = \Carbon\Carbon::parse($end_date)->diffInDays(\Carbon\Carbon::parse($start_date)) + 1;
            $comparison_start = \Carbon\Carbon::parse($start_date)->subDays($days)->format('Y-m-d');
            $comparison_end = \Carbon\Carbon::parse($start_date)->subDay()->format('Y-m-d');
        } elseif ($compare_with === 'previous_year') {
            $comparison_start = \Carbon\Carbon::parse($start_date)->subYear()->format('Y-m-d');
            $comparison_end = \Carbon\Carbon::parse($end_date)->subYear()->format('Y-m-d');
        }

        $comparison_data = $this->calculateProfitLossData($comparison_start, $comparison_end);
    }

    // Calculate current period data
    $current_data = $this->calculateProfitLossData($start_date, $end_date);

    return view('inventory::Reports/ProfitNLoss', [
        'current_data' => $current_data,
        'comparison_data' => $comparison_data,
        'filters' => [
            'date_range' => $date_range,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'compare_with' => $compare_with
        ],
        'period_labels' => [
            'current' => \Carbon\Carbon::parse($start_date)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($end_date)->format('M d, Y'),
            'comparison' => $comparison_start ? \Carbon\Carbon::parse($comparison_start)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($comparison_end)->format('M d, Y') : null
        ]
    ]);
}

    private function calculateProfitLossData($start_date, $end_date)
    {
        // Calculate revenue from sales
        $revenue = \DB::table('sales as s')
            ->join('sale_items as si', 's.id', '=', 'si.sale_id')
            ->whereBetween('s.date', [$start_date, $end_date])
            ->sum('si.subtotal');

        // Calculate cost of goods sold (COGS)
        $cogs = \DB::table('sales as s')
            ->join('sale_items as si', 's.id', '=', 'si.sale_id')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->whereBetween('s.date', [$start_date, $end_date])
            ->sum(\DB::raw('si.quantity * p.cost_price'));

        // Calculate gross profit
        $gross_profit = $revenue - $cogs;

        // Calculate operating expenses (you'll need an expenses table)
        $expenses = \DB::table('expenses')
            ->whereBetween('date', [$start_date, $end_date])
            ->sum('amount');

        // Calculate net profit
        $net_profit = $gross_profit - $expenses;

        // Calculate returns and allowances
        $returns = \DB::table('sale_returns as sr')
            ->join('sale_return_items as sri', 'sr.id', '=', 'sri.sale_return_id')
            ->whereBetween('sr.return_date', [$start_date, $end_date])
            ->sum('sri.subtotal');

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $gross_profit,
            'gross_margin' => $revenue > 0 ? ($gross_profit / $revenue) * 100 : 0,
            'expenses' => $expenses,
            'net_profit' => $net_profit,
            'net_margin' => $revenue > 0 ? ($net_profit / $revenue) * 100 : 0,
            'returns' => $returns,
            'net_revenue' => $revenue - $returns
        ];
    }

    //Accounts Payable

    public function AccountsPayable(Request $request)
    {
        $data    = array();

        return view('inventory::Reports/AccountsPayable', $data);
    }


    //Accounts Receivable
    public function AccountsReceivable(Request $request)
    {
        $data    = array();

        return view('inventory::Reports/AccountsReceivable', $data);
    }

}
