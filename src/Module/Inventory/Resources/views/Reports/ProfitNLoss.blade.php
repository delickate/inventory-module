@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Profit & Loss Statement</h2>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('inventoryReports.ProfitNLoss') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="date_range" class="form-control" id="dateRangeSelect">
                                    <option value="today" {{ $filters['date_range'] == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ $filters['date_range'] == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="this_week" {{ $filters['date_range'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="last_week" {{ $filters['date_range'] == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="this_month" {{ $filters['date_range'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ $filters['date_range'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_quarter" {{ $filters['date_range'] == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                    <option value="last_quarter" {{ $filters['date_range'] == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
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
                            <div class="col-md-3">
                                <select name="compare_with" class="form-control">
                                    <option value="none" {{ $filters['compare_with'] == 'none' ? 'selected' : '' }}>No Comparison</option>
                                    <option value="previous_period" {{ $filters['compare_with'] == 'previous_period' ? 'selected' : '' }}>Compare with Previous Period</option>
                                    <option value="previous_year" {{ $filters['compare_with'] == 'previous_year' ? 'selected' : '' }}>Compare with Previous Year</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="{{ route('inventoryReports.ProfitNLoss') }}" class="btn btn-secondary">Reset</a>
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
            <!-- Period Information -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h4>
                        Period: {{ $period_labels['current'] }}
                        @if($comparison_data)
                            <small class="text-muted">vs. {{ $period_labels['comparison'] }}</small>
                        @endif
                    </h4>
                </div>
            </div>
            
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Net Revenue</h5>
                            <p class="card-text h4">{{ number_format($current_data['net_revenue'], 2) }}</p>
                            @if($comparison_data)
                                <p class="card-text small">
                                    @php
                                        $change = $current_data['net_revenue'] - $comparison_data['net_revenue'];
                                        $percent = $comparison_data['net_revenue'] != 0 ? ($change / $comparison_data['net_revenue']) * 100 : 0;
                                    @endphp
                                    <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }} ({{ number_format(abs($percent), 2) }}%)
                                    </span>
                                    vs. previous
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Gross Profit</h5>
                            <p class="card-text h4">{{ number_format($current_data['gross_profit'], 2) }}</p>
                            @if($comparison_data)
                                <p class="card-text small">
                                    @php
                                        $change = $current_data['gross_profit'] - $comparison_data['gross_profit'];
                                        $percent = $comparison_data['gross_profit'] != 0 ? ($change / $comparison_data['gross_profit']) * 100 : 0;
                                    @endphp
                                    <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }} ({{ number_format(abs($percent), 2) }}%)
                                    </span>
                                    vs. previous
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Net Profit</h5>
                            <p class="card-text h4">{{ number_format($current_data['net_profit'], 2) }}</p>
                            @if($comparison_data)
                                <p class="card-text small">
                                    @php
                                        $change = $current_data['net_profit'] - $comparison_data['net_profit'];
                                        $percent = $comparison_data['net_profit'] != 0 ? ($change / $comparison_data['net_profit']) * 100 : 0;
                                    @endphp
                                    <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }} ({{ number_format(abs($percent), 2) }}%)
                                    </span>
                                    vs. previous
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Net Margin</h5>
                            <p class="card-text h4">{{ number_format($current_data['net_margin'], 2) }}%</p>
                            @if($comparison_data)
                                <p class="card-text small">
                                    @php
                                        $change = $current_data['net_margin'] - $comparison_data['net_margin'];
                                    @endphp
                                    <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }}%
                                    </span>
                                    vs. previous
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profit & Loss Statement -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered" id="profitLossTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Amount</th>
                            @if($comparison_data)
                                <th class="text-right">Previous Amount</th>
                                <th class="text-right">Change</th>
                                <th class="text-right">% Change</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Revenue Section -->
                        <tr class="table-active">
                            <td colspan="{{ $comparison_data ? 5 : 2 }}"><strong>Revenue</strong></td>
                        </tr>
                        <tr>
                            <td>Gross Sales</td>
                            <td class="text-right">{{ number_format($current_data['revenue'], 2) }}</td>
                            @if($comparison_data)
                                <td class="text-right">{{ number_format($comparison_data['revenue'], 2) }}</td>
                                <td class="text-right {{ ($current_data['revenue'] - $comparison_data['revenue']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($current_data['revenue'] - $comparison_data['revenue'], 2) }}
                                </td>
                                <td class="text-right {{ ($current_data['revenue'] - $comparison_data['revenue']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison_data['revenue'] != 0 ? number_format((($current_data['revenue'] - $comparison_data['revenue']) / $comparison_data['revenue'] * 100), 2) : 'N/A' }}%
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>Returns & Allowances</td>
                            <td class="text-right text-danger">({{ number_format($current_data['returns'], 2) }})</td>
                            @if($comparison_data)
                                <td class="text-right text-danger">({{ number_format($comparison_data['returns'], 2) }})</td>
                                <td class="text-right {{ ($current_data['returns'] - $comparison_data['returns']) <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($current_data['returns'] - $comparison_data['returns'], 2) }}
                                </td>
                                <td class="text-right {{ ($current_data['returns'] - $comparison_data['returns']) <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison_data['returns'] != 0 ? number_format((($current_data['returns'] - $comparison_data['returns']) / $comparison_data['returns'] * 100), 2) : 'N/A' }}%
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td><strong>Net Revenue</strong></td>
                            <td class="text-right"><strong>{{ number_format($current_data['net_revenue'], 2) }}</strong></td>
                            @if($comparison_data)
                                <td class="text-right"><strong>{{ number_format($comparison_data['net_revenue'], 2) }}</strong></td>
                                <td class="text-right {{ ($current_data['net_revenue'] - $comparison_data['net_revenue']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($current_data['net_revenue'] - $comparison_data['net_revenue'], 2) }}</strong>
                                </td>
                                <td class="text-right {{ ($current_data['net_revenue'] - $comparison_data['net_revenue']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ $comparison_data['net_revenue'] != 0 ? number_format((($current_data['net_revenue'] - $comparison_data['net_revenue']) / $comparison_data['net_revenue'] * 100), 2) : 'N/A' }}%</strong>
                                </td>
                            @endif
                        </tr>
                        
                        <!-- Cost of Goods Sold -->
                        <tr class="table-active">
                            <td colspan="{{ $comparison_data ? 5 : 2 }}"><strong>Cost of Goods Sold</strong></td>
                        </tr>
                        <tr>
                            <td>Cost of Products Sold</td>
                            <td class="text-right">{{ number_format($current_data['cogs'], 2) }}</td>
                            @if($comparison_data)
                                <td class="text-right">{{ number_format($comparison_data['cogs'], 2) }}</td>
                                <td class="text-right {{ ($current_data['cogs'] - $comparison_data['cogs']) <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($current_data['cogs'] - $comparison_data['cogs'], 2) }}
                                </td>
                                <td class="text-right {{ ($current_data['cogs'] - $comparison_data['cogs']) <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison_data['cogs'] != 0 ? number_format((($current_data['cogs'] - $comparison_data['cogs']) / $comparison_data['cogs'] * 100), 2) : 'N/A' }}%
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td><strong>Gross Profit</strong></td>
                            <td class="text-right"><strong>{{ number_format($current_data['gross_profit'], 2) }}</strong></td>
                            @if($comparison_data)
                                <td class="text-right"><strong>{{ number_format($comparison_data['gross_profit'], 2) }}</strong></td>
                                <td class="text-right {{ ($current_data['gross_profit'] - $comparison_data['gross_profit']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($current_data['gross_profit'] - $comparison_data['gross_profit'], 2) }}</strong>
                                </td>
                                <td class="text-right {{ ($current_data['gross_profit'] - $comparison_data['gross_profit']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ $comparison_data['gross_profit'] != 0 ? number_format((($current_data['gross_profit'] - $comparison_data['gross_profit']) / $comparison_data['gross_profit'] * 100), 2) : 'N/A' }}%</strong>
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>Gross Margin</td>
                            <td class="text-right">{{ number_format($current_data['gross_margin'], 2) }}%</td>
                            @if($comparison_data)
                                <td class="text-right">{{ number_format($comparison_data['gross_margin'], 2) }}%</td>
                                <td class="text-right {{ ($current_data['gross_margin'] - $comparison_data['gross_margin']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($current_data['gross_margin'] - $comparison_data['gross_margin'], 2) }}%
                                </td>
                                <td class="text-right">-</td>
                            @endif
                        </tr>
                        
                        <!-- Operating Expenses -->
                        <tr class="table-active">
                            <td colspan="{{ $comparison_data ? 5 : 2 }}"><strong>Operating Expenses</strong></td>
                        </tr>
                        <tr>
                            <td>Total Expenses</td>
                            <td class="text-right">{{ number_format($current_data['expenses'], 2) }}</td>
                            @if($comparison_data)
                                <td class="text-right">{{ number_format($comparison_data['expenses'], 2) }}</td>
                                <td class="text-right {{ ($current_data['expenses'] - $comparison_data['expenses']) <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($current_data['expenses'] - $comparison_data['expenses'], 2) }}
                                </td>
                                <td class="text-right {{ ($current_data['expenses'] - $comparison_data['expenses']) <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison_data['expenses'] != 0 ? number_format((($current_data['expenses'] - $comparison_data['expenses']) / $comparison_data['expenses'] * 100), 2) : 'N/A' }}%
                                </td>
                            @endif
                        </tr>
                        
                        <!-- Net Profit -->
                        <tr class="table-active">
                            <td><strong>Net Profit</strong></td>
                            <td class="text-right"><strong>{{ number_format($current_data['net_profit'], 2) }}</strong></td>
                            @if($comparison_data)
                                <td class="text-right"><strong>{{ number_format($comparison_data['net_profit'], 2) }}</strong></td>
                                <td class="text-right {{ ($current_data['net_profit'] - $comparison_data['net_profit']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($current_data['net_profit'] - $comparison_data['net_profit'], 2) }}</strong>
                                </td>
                                <td class="text-right {{ ($current_data['net_profit'] - $comparison_data['net_profit']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ $comparison_data['net_profit'] != 0 ? number_format((($current_data['net_profit'] - $comparison_data['net_profit']) / $comparison_data['net_profit'] * 100), 2) : 'N/A' }}%</strong>
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>Net Margin</td>
                            <td class="text-right">{{ number_format($current_data['net_margin'], 2) }}%</td>
                            @if($comparison_data)
                                <td class="text-right">{{ number_format($comparison_data['net_margin'], 2) }}%</td>
                                <td class="text-right {{ ($current_data['net_margin'] - $comparison_data['net_margin']) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($current_data['net_margin'] - $comparison_data['net_margin'], 2) }}%
                                </td>
                                <td class="text-right">-</td>
                            @endif
                        </tr>
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

// Excel export
document.getElementById('exportBtn').addEventListener('click', function() {
    let table = document.getElementById('profitLossTable');
    let html = table.outerHTML;
    
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Profit_Loss_Statement_' + new Date().toISOString().slice(0, 10) + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
</script>
@endsection