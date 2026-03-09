@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Return Items for Purchase #{{ $purchase->invoice_no }}</h3>
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

            <form action="{{ route('purchases.returnProcess', $purchase->id) }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Supplier</label>
                            <p class="form-control-plaintext">{{ optional($purchase->suppliers)->name }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Return Date *</label>
                            <input type="date" name="return_date" class="form-control" 
                                   value="{{ old('return_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Reason for Return *</label>
                    <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Product</th>
                                <th>Original Qty</th>
                                <th>Unit Price</th>
                                <th>Return Qty *</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                            <tr>
                                <td>{{ optional($item->products)->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                    <input type="number" name="items[{{ $item->id }}][return_qty]" 
                                           class="form-control return-qty" min="0" max="{{ $item->quantity }}" 
                                           value="0" data-unit-price="{{ $item->unit_price }}">
                                </td>
                                <td class="subtotal">0.00</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total Return Amount</th>
                                <th id="total-amount">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Process Return</button>
                    <a href="{{ route('purchases.listing', $purchase->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const returnQtyInputs = document.querySelectorAll('.return-qty');
    
    function calculateTotals() {
        let total = 0;
        
        returnQtyInputs.forEach(input => {
            const row = input.closest('tr');
            const qty = parseFloat(input.value) || 0;
            const unitPrice = parseFloat(input.dataset.unitPrice);
            const subtotal = qty * unitPrice;
            
            row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            total += subtotal;
        });
        
        document.getElementById('total-amount').textContent = total.toFixed(2);
    }
    
    returnQtyInputs.forEach(input => {
        input.addEventListener('input', calculateTotals);
    });
    
    calculateTotals(); // Initialize
});
</script>
@endsection
@endsection