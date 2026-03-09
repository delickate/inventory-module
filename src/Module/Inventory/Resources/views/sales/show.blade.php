@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Sale Details #{{ $sale->id }}</h3>
        </div>
        
        <div class="card-body">

            @if ($errors->any())
  <ul class='alert alert-danger'>
      @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
      @endforeach
  </ul>
@endif
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

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Customer</label>
                        <p class="form-control-plaintext">{{ optional($sale->customers)->name }}</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date</label>
                        <p class="form-control-plaintext">{{ $sale->date }}</p>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->Items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($item->products)->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total Amount</th>
                            <th>{{ number_format($sale->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('sales.returnForm', $sale->id) }}" class="btn btn-warning">
                    <i class="fas fa-undo"></i> Process Return
                </a>
                <a href="{{ route('sales.editing', $sale->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('sales.listing') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            
            @if($sale->returns->count() > 0)
            <div class="mt-5">
                <h4>Return History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Return Date</th>
                                <th>Reason</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->returns as $return)
                            <tr>
                                <td>{{ $return->return_date }}</td>
                                <td>{{ $return->reason }}</td>
                                <td>{{ number_format($return->total_amount, 2) }}</td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection