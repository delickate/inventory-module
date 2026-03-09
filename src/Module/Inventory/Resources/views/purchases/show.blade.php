@extends('layouts.app')

@section('content')

<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">

                        

                        <div class="card">
                            <div class="card-body">
<?php if(Session::has('success_message')){ ?>
  <div class='alert alert-success alert-dismissable text-left'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <i class='icon fa fa-check'></i>Success: <?php echo Session::get('success_message');?>
  </div>

<?php }elseif(Session::has('error_message')){ ?>
  <div class='alert alert-danger alert-dismissable text-left'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <i class='icon fa fa-ban'></i>Error: <?php echo Session::get('error_message');?>
  </div> 
<?php } ?>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Purchase Voucher #{{ $purchase->invoice_no }}</h3>
        </div>
        
        <div class="card-body">
            <!-- Display errors if any -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ $is_approved = $purchase->is_approved }}
            <!-- Basic Information Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Invoice Number</label>
                        <p class="form-control-plaintext">{{ $purchase->invoice_no }}</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Purchase Date</label>
                        <p class="form-control-plaintext">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Total Amount</label>
                        <p class="form-control-plaintext">{{ number_format($purchase->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Supplier Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Supplier Name</label>
                                    <p>{{ optional($purchase->suppliers)->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    @if(optional($purchase->suppliers)->phone)
                                        <label class="font-weight-bold">Contact</label>
                                        <p>{{ optional($purchase->suppliers)->phone }}</p>
                                    @endif
                                </div>
                            </div>
                            @if(optional($purchase->suppliers)->address)
                                <label class="font-weight-bold">Address</label>
                                <p>{{ optional($purchase->suppliers)->address }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Payment Status</label>
                                    <p>
                                        @if($purchase->is_fully_paid)
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Payment Date</label>
                                    <p>
                                        @if($purchase->payment_date)
                                            {{ \Carbon\Carbon::parse($purchase->payment_date)->format('d M, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Purchased Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="45%">Product</th>
                                    <th width="10%">Quantity</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="20%">Total</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ optional($item->product)->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Subtotal</th>
                                    <th class="text-right">{{ number_format($purchase->items->sum('price'), 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Tax (if any)</th>
                                    <th class="text-right">{{ number_format($purchase->tax_amount ?? 0, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Grand Total</th>
                                    <th class="text-right">{{ number_format($purchase->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            @if($purchase->notes)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Additional Notes</h5>
                </div>
                <div class="card-body">
                    <p>{{ $purchase->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('purchases.listing') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                
                
            </div>
        </div>
    </div>
</div>


                </div>
             </div>
          </div>
        </div>
     </div>
  </div> 

@endsection

@section('styles')
<style>
    .form-control-plaintext {
        padding: 0.375rem 0;
        margin-bottom: 0;
        background-color: transparent;
        border: solid transparent;
        border-width: 1px 0;
    }
    .table thead th {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85em;
        font-weight: 500;
    }
</style>
@endsection