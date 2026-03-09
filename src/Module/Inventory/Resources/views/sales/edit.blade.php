@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Edit Sale #{{ $sale->id }}</h3>
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

            <form action="{{ route('sales.updating', $sale->id) }}" method="POST" id="saleForm">
                @csrf
                @method('POST')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer *</label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date *</label>
                            <input type="date" name="date" class="form-control" required 
                                   value="{{ old('date', $sale->date) }}">
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $index }}][product_id]" class="form-control product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-price="{{ $product->price }}"
                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" 
                                           min="1" value="{{ old("items.$index.quantity", $item->quantity) }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][unit_price]" class="form-control unit-price" 
                                           step="0.01" min="0" value="{{ old("items.$index.unit_price", $item->unit_price) }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal" 
                                           value="{{ number_format($item->quantity * $item->unit_price, 2) }}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger remove-row">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="button" id="addRow" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <h4>Total Amount: <span id="totalAmount">{{ number_format($sale->total_amount, 2) }}</span></h4>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Update Sale</button>
                    <a href="{{ route('sales.showing', $sale->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add new row
    document.getElementById('addRow').addEventListener('click', function() {
        const tbody = document.querySelector('#itemsTable tbody');
        const rows = document.querySelectorAll('.item-row');
        const newRow = rows[0].cloneNode(true);
        const rowIndex = rows.length;
        
        // Clear values
        newRow.querySelector('.product-select').selectedIndex = 0;
        newRow.querySelector('.quantity').value = 1;
        newRow.querySelector('.unit-price').value = '';
        newRow.querySelector('.subtotal').value = '0.00';
        
        // Update names with new index
        newRow.querySelectorAll('[name]').forEach(el => {
            const name = el.getAttribute('name').replace(/\[\d+\]/g, `[${rowIndex}]`);
            el.setAttribute('name', name);
        });
        
        tbody.appendChild(newRow);
    });
    
    // Remove row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                calculateTotal();
            }
        }
    });
    
    // Calculate row subtotal
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const row = e.target.closest('tr');
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            row.querySelector('.subtotal').value = (qty * price).toFixed(2);
            calculateTotal();
        }
    });
    
    // Set unit price when product selected
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.dataset.price;
            const row = e.target.closest('tr');
            const unitPriceInput = row.querySelector('.unit-price');
            
            if (price) {
                unitPriceInput.value = price;
            }
            // Trigger input event to calculate subtotal
            unitPriceInput.dispatchEvent(new Event('input'));
        }
    });
    
    // Calculate total amount
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('totalAmount').textContent = total.toFixed(2);
    }
    
    // Initialize calculations for existing rows
    calculateTotal();
});
</script>

@endsection