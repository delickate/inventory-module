@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Low Stock Alert</h2>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('inventoryReports.LowStockAlert') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="warehouse_id" class="form-control">
                                    <option value="">All Warehouses</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $filters['warehouse_id'] == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $filters['category_id'] == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="severity" class="form-control">
                                    <option value="all" {{ $filters['severity'] == 'all' ? 'selected' : '' }}>All Alerts</option>
                                    <option value="critical" {{ $filters['severity'] == 'critical' ? 'selected' : '' }}>Critical Only</option>
                                    <option value="warning" {{ $filters['severity'] == 'warning' ? 'selected' : '' }}>Warning Only</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('inventoryReports.LowStockAlert') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-right">
                    <a href="#" class="btn btn-success" onclick="window.print()">Print Report</a>
                    <a href="#" class="btn btn-info" id="exportBtn">Export to Excel</a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if($low_stock_items->isEmpty())
                <div class="alert alert-info">No low stock items found based on current filters.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="lowStockTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Status</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Warehouse</th>
                                <th>Current Qty</th>
                                <th>Min Qty</th>
                                <th>Shortage</th>
                                <th>Reorder Qty</th>
                                <th>Stock Level</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($low_stock_items as $key => $item)
                            <tr class="@if($item->stock_status == 'critical') table-danger @elseif($item->stock_status == 'warning') table-warning @endif">
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if($item->stock_status == 'critical')
                                        <span class="badge badge-danger">Critical</span>
                                    @else
                                        <span class="badge badge-warning">Warning</span>
                                    @endif
                                </td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $item->category_name }}</td>
                                <td>{{ $item->warehouse_name }}</td>
                                <td class="text-right">{{ number_format($item->quantity) }}</td>
                                <td class="text-right">{{ number_format($item->minimum_quantity) }}</td>
                                <td class="text-right">{{ number_format($item->shortage) }}</td>
                                <td class="text-right">{{ number_format($item->reorder_quantity) }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar @if($item->stock_level_percent < 50) bg-danger @else bg-warning @endif" 
                                             role="progressbar" 
                                             style="width: {{ min(100, $item->stock_level_percent) }}%" 
                                             aria-valuenow="{{ $item->stock_level_percent }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ round($item->stock_level_percent) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->unit_name }}</td>
                                <td>
                                    <a href="{{ route('purchase.create', ['product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->reorder_quantity]) }}" 
                                       class="btn btn-sm btn-primary" title="Create Purchase Order">
                                        <i class="fas fa-cart-plus"></i> PO
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for Excel export -->
<script>
document.getElementById('exportBtn').addEventListener('click', function() {
    // Convert table to Excel
    let table = document.getElementById('lowStockTable');
    let html = table.outerHTML;
    
    // Create download link
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Low_Stock_Alert_' + new Date().toISOString().slice(0, 10) + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
</script>

<style>
.progress {
    background-color: #f0f0f0;
    border-radius: 4px;
}
.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endsection