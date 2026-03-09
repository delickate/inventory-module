@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Customer Purchase History</h2>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('inventoryReports.CustomerPurchaseHistory') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="customer_id" class="form-control">
                                    <option value="">All Customers</option>
                                    @foreach($all_customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $filters['customer_id'] == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                                <select name="sort_by" class="form-control">
                                    <option value="recent" {{ $filters['sort_by'] == 'recent' ? 'selected' : '' }}>Recent First</option>
                                    <option value="highest" {{ $filters['sort_by'] == 'highest' ? 'selected' : '' }}>Highest Spenders</option>
                                    <option value="frequent" {{ $filters['sort_by'] == 'frequent' ? 'selected' : '' }}>Most Frequent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="{{ route('inventoryReports.CustomerPurchaseHistory') }}" class="btn btn-secondary">Reset</a>
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
                            <h5 class="card-title">Total Customers</h5>
                            <p class="card-text h4">{{ $summary_totals['total_customers'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text h4">{{ number_format($summary_totals['total_sales'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text h4">{{ $summary_totals['total_orders'] }}</p>
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
            
            <!-- Customer Purchase Summary -->
            <div class="table-responsive mb-5">
                <table class="table table-bordered" id="customerTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th class="text-right">Orders</th>
                            <th class="text-right">Total Spent</th>
                            <th class="text-right">Avg. Order</th>
                            <th class="text-right">Items</th>
                            <th>Last Purchase</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>
                                @if($customer->phone)
                                    {{ $customer->phone }}<br>
                                @endif
                                @if($customer->email)
                                    <small>{{ $customer->email }}</small>
                                @endif
                            </td>
                            <td class="text-right">{{ $customer->purchase_count }}</td>
                            <td class="text-right">{{ number_format($customer->total_spent, 2) }}</td>
                            <td class="text-right">{{ number_format($customer->avg_order_value, 2) }}</td>
                            <td class="text-right">{{ $customer->total_items_purchased }}</td>
                            <td>
                                @if($customer->last_purchase_date)
                                    {{ \Carbon\Carbon::parse($customer->last_purchase_date)->format('M d, Y') }}
                                @else
                                    Never
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info toggle-details" data-customer-id="{{ $customer->id }}">
                                    <i class="fas fa-chevron-down"></i> Details
                                </button>
                            </td>
                        </tr>
                        <!-- Detailed Purchase History (hidden by default) -->
                        <tr class="detail-row" id="details-{{ $customer->id }}" style="display: none;">
                            <td colspan="9">
                                <div class="p-3">
                                    <h5>Purchase History for {{ $customer->name }}</h5>
                                    @if(isset($detailed_history[$customer->id]) && $detailed_history[$customer->id]->count())
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Order #</th>
                                                    <th>Product</th>
                                                    <th>SKU</th>
                                                    <th class="text-right">Qty</th>
                                                    <th class="text-right">Price</th>
                                                    <th class="text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($detailed_history[$customer->id] as $item)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}</td>
                                                    <td>#{{ str_pad($item->sale_id, 6, '0', STR_PAD_LEFT) }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td class="text-right">{{ $item->quantity }}</td>
                                                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info">No purchase history found for this customer in the selected period.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
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

// Toggle detailed purchase history
document.querySelectorAll('.toggle-details').forEach(button => {
    button.addEventListener('click', function() {
        const customerId = this.getAttribute('data-customer-id');
        const detailRow = document.getElementById(`details-${customerId}`);
        const icon = this.querySelector('i');
        
        if (detailRow.style.display === 'none') {
            detailRow.style.display = 'table-row';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            this.innerHTML = '<i class="fas fa-chevron-up"></i> Hide';
        } else {
            detailRow.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            this.innerHTML = '<i class="fas fa-chevron-down"></i> Details';
        }
    });
});

// Excel export
document.getElementById('exportBtn').addEventListener('click', function() {
    let table = document.getElementById('customerTable');
    let html = table.outerHTML;
    
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Customer_Purchase_History_' + new Date().toISOString().slice(0, 10) + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
</script>
@endsection