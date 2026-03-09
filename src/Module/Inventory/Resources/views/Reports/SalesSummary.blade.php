@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Sales Summary Report</h2>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('inventoryReports.SalesSummary') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="date_range" class="form-control" id="dateRangeSelect">
                                    <option value="today" {{ $filters['date_range'] == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ $filters['date_range'] == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="this_week" {{ $filters['date_range'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="last_week" {{ $filters['date_range'] == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="this_month" {{ $filters['date_range'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ $filters['date_range'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_year" {{ $filters['date_range'] == 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="last_year" {{ $filters['date_range'] == 'last_year' ? 'selected' : '' }}>Last Year</option>
                                    <option value="custom" {{ $filters['date_range'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-2 custom-date" style="{{ $filters['date_range'] != 'custom' ? 'display: none;' : '' }}">
                                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                            </div>
                            <div class="col-md-2 custom-date" style="{{ $filters['date_range'] != 'custom' ? 'display: none;' : '' }}">
                                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                            </div>
                            <div class="col-md-2">
                                <select name="group_by" class="form-control">
                                    <option value="day" {{ $filters['group_by'] == 'day' ? 'selected' : '' }}>By Day</option>
                                    <option value="week" {{ $filters['group_by'] == 'week' ? 'selected' : '' }}>By Week</option>
                                    <option value="month" {{ $filters['group_by'] == 'month' ? 'selected' : '' }}>By Month</option>
                                    <option value="year" {{ $filters['group_by'] == 'year' ? 'selected' : '' }}>By Year</option>
                                    <option value="product" {{ $filters['group_by'] == 'product' ? 'selected' : '' }}>By Product</option>
                                    <option value="customer" {{ $filters['group_by'] == 'customer' ? 'selected' : '' }}>By Customer</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="customer_id" class="form-control">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $filters['customer_id'] == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="{{ route('inventoryReports.SalesSummary') }}" class="btn btn-secondary">Reset</a>
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
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text h4">{{ number_format($summary_totals['total_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Orders</h5>
                            <p class="card-text h4">{{ number_format($summary_totals['order_count']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Items Sold</h5>
                            <p class="card-text h4">{{ number_format($summary_totals['total_quantity']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Avg. Order Value</h5>
                            <p class="card-text h4">{{ number_format($summary_totals['avg_order_value'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sales Data Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="salesTable">
                    <thead class="thead-dark">
                        <tr>
                            @switch($filters['group_by'])
                                @case('product')
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Orders</th>
                                    @break
                                    
                                @case('customer')
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Quantity</th>
                                    @break
                                    
                                @case('week')
                                    <th>#</th>
                                    <th>Week</th>
                                    <th>Date Range</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Quantity</th>
                                    @break
                                    
                                @case('month')
                                    <th>#</th>
                                    <th>Month</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Quantity</th>
                                    @break
                                    
                                @case('year')
                                    <th>#</th>
                                    <th>Year</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Quantity</th>
                                    @break
                                    
                                @default <!-- day -->
                                    <th>#</th>
                                    <th>Date</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Quantity</th>
                            @endswitch
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales_data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            
                            @switch($filters['group_by'])
                                @case('product')
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td class="text-right">{{ number_format($item->total_quantity) }}</td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->order_count) }}</td>
                                    @break
                                    
                                @case('customer')
                                    <td>{{ $item->customer_name }}</td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->order_count) }}</td>
                                    <td class="text-right">{{ number_format($item->total_quantity) }}</td>
                                    @break
                                    
                                @case('week')
                                    <td>{{ $item->year }}-W{{ str_pad($item->week, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->week_start)->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($item->week_end)->format('M d') }}
                                    </td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->order_count) }}</td>
                                    <td class="text-right">{{ number_format($item->total_quantity) }}</td>
                                    @break
                                    
                                @case('month')
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $item->month_name)->format('F Y') }}</td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->order_count) }}</td>
                                    <td class="text-right">{{ number_format($item->total_quantity) }}</td>
                                    @break
                                    
                                @case('year')
                                    <td>{{ $item->year }}</td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->order_count) }}</td>
                                    <td class="text-right">{{ number_format($item->total_quantity) }}</td>
                                    @break
                                    
                                @default <!-- day -->
                                    <td>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}</td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->order_count) }}</td>
                                    <td class="text-right">{{ number_format($item->total_quantity) }}</td>
                            @endswitch
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="{{ $filters['group_by'] == 'product' ? 3 : 2 }}" class="text-right">Totals:</td>
                            @if($filters['group_by'] == 'product')
                                <td class="text-right">{{ number_format($summary_totals['total_quantity']) }}</td>
                            @endif
                            <td class="text-right">{{ number_format($summary_totals['total_amount'], 2) }}</td>
                            <td class="text-right">{{ number_format($summary_totals['order_count']) }}</td>
                            @if($filters['group_by'] != 'product')
                                <td class="text-right">{{ number_format($summary_totals['total_quantity']) }}</td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Toggle custom date fields
document.getElementById('dateRangeSelect').addEventListener('change', function() {
    const customDateFields = document.querySelectorAll('.custom-date');
    if (this.value === 'custom') {
        customDateFields.forEach(field => field.style.display = 'block');
    } else {
        customDateFields.forEach(field => field.style.display = 'none');
    }
});

// Excel export
document.getElementById('exportBtn').addEventListener('click', function() {
    let table = document.getElementById('salesTable');
    let html = table.outerHTML;
    
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Sales_Summary_' + new Date().toISOString().slice(0, 10) + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
</script>
@endsection