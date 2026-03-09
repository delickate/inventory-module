@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Current Stock Status</h2>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('inventoryReports.CurrentStockStatus') }}">
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
                                <input type="text" name="search" class="form-control" placeholder="Search product..." value="{{ $filters['search'] }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('inventoryReports.CurrentStockStatus') }}" class="btn btn-secondary">Reset</a>
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
            <div class="table-responsive">
                <table class="table table-bordered" id="stockTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Barcode</th>
                            <th>Category</th>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Cost Price</th>
                            <th>Sale Price</th>
                            <th>Cost Value</th>
                            <th>Sale Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->barcode }}</td>
                            <td>{{ $item->category_name }}</td>
                            <td>{{ $item->warehouse_name }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ $item->unit_name }}</td>
                            <td class="text-right">{{ number_format($item->cost_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->sale_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->inventory_cost_value, 2) }}</td>
                            <td class="text-right">{{ number_format($item->inventory_sale_value, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="10" class="text-right">Totals:</td>
                            <td class="text-right">{{ number_format($total_cost_value, 2) }}</td>
                            <td class="text-right">{{ number_format($total_sale_value, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Excel export -->
<script>
document.getElementById('exportBtn').addEventListener('click', function() {
    // Convert table to Excel
    let table = document.getElementById('stockTable');
    let html = table.outerHTML;
    
    // Create download link
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Current_Stock_Status_' + new Date().toISOString().slice(0, 10) + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
</script>
@endsection