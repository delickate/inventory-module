@extends('layouts.app')
@section('content')
<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">

                        

                        <div class="card">
                            <div class="card-body">

@if ($errors->any())
  <ul class='alert alert-danger'>
      @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
      @endforeach
  </ul>
@endif
 <div class='row'>
            <form action="{{ route('purchases.updating', $purchase->id) }}" method="POST" enctype="multipart/form-data" id="purchaseForm">
    @csrf
    @method('POST')

    <div class='col-xs-12 col-sm-12 col-md-12'>
        <div class='form-group'>
            <strong>Suppliers:</strong>
            <select class='form-control' name='form_supplier_id' id='form_supplier_id' required>
                <option value=''>Select Supplier</option>
                @if($suppliers)
                    @foreach($suppliers as $key => $val)
                        <option value='{{ $key }}' {{ $purchase->supplier_id == $key ? 'selected="selected"' : '' }}>
                            {{ $val }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class='col-xs-12 col-sm-12 col-md-12'>
        <div class='form-group'>
            <strong>Purchase Date:</strong>
            <input type='date' name='form_purchase_date' value='{{ old('form_purchase_date', $purchase->purchase_date) }}' class='form-control' required />
        </div>
    </div>

    <div class='col-xs-12 col-sm-12 col-md-12'>
        <div class='form-group'>
            <strong>Invoice No:</strong>
            <input type='text' name='form_invoice_no' value='{{ old('form_invoice_no', $purchase->invoice_no) }}' class='form-control' placeholder='Invoice no' required />
        </div>
    </div>
                
    <div class='col-xs-12 col-sm-12 col-md-12'>
        <div class='form-group'>
            <strong>Total Amount:</strong>
            <input type='number' name='form_total_amount' value='{{ old('form_total_amount', $purchase->total_amount) }}' class='form-control' placeholder='Total amount' step="0.01" min="0" required />
        </div>
    </div>
       
     <div class='col-xs-12 col-sm-12 col-md-12'>             
    <fieldset>
        <legend>Items:</legend>
        <div class="widget">
            <button type="button" class="btn btn-success pull-right m-b-10" onClick="addMoreRow()">+ Add More</button>
            
            <div class="widgetcontent nopadding">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th width="40%">Products</th>
                            <th>Quantity</th>
                            <th>Unit price</th>
                            <th>Total</th>
                            <th width="60">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                        <tr class="item-row">
                            <td>
                                <select class='form-control product-select' name='form_product_id[]' required>
                                    <option value=''>Select Product</option>
                                    @if($products)
                                        @foreach($products as $key => $val)
                                            <option value='{{ $key }}' {{ $item->product_id == $key ? 'selected="selected"' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td>
                                <input type='number' name='form_quantity[]' min="1" value='{{ old("form_quantity.$index", $item->quantity) }}' class='form-control quantity' placeholder='Quantity' required>
                            </td>
                            <td>
                                <input type='number' name='form_unit_price[]' min="0" step="0.01" value='{{ old("form_unit_price.$index", $item->unit_price) }}' class='form-control unit-price' placeholder='Unit price' required>
                            </td>
                            <td>
                                <input type='number' name='form_price[]' value='{{ old("form_price.$index", $item->total_amount) }}' class='form-control price' readonly>
                            </td>
                            <td valign="middle" align="center">
                                <button type="button" class="btn btn_less" onclick="removeRow(this)">Remove</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <strong>Items Total: </strong><span id="itemsTotal">{{ $purchase->items->sum('price') }}</span>
                </div>
            </div>
        </div>
    </fieldset>
    </div>
    <div class='col-xs-12 col-sm-12 col-md-12 text-center'>
        <button type='submit' class='btn btn-primary'>Update</button>
        <a href="{{ route('purchases.listing') }}" class='btn btn-primary'>Back</a>
    </div>
</form>



                                </div>
             </div>
          </div>
        </div>
     </div>
  </div>

  <script type="text/javascript">
      

document.addEventListener('DOMContentLoaded', function() {
    // Calculate totals when page loads
    calculateTotals();
    
    // Add event listeners for dynamic calculations
    document.getElementById('itemsTable').addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            calculateRowTotal(e.target.closest('tr'));
            calculateTotals();
        }
    });
    
    // Form submission validation
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const totalAmount = parseFloat(document.querySelector('input[name="form_total_amount"]').value) || 0;
        const itemsTotal = parseFloat(document.getElementById('itemsTotal').textContent) || 0;
        
        if (itemsTotal > totalAmount) {
            e.preventDefault();
            alert('The sum of purchase items exceeds the total amount. Please adjust your entries.');
            return false;
        }
        
        if (itemsTotal <= 0) {
            e.preventDefault();
            alert('Please add at least one valid purchase item.');
            return false;
        }
        
        return true;
    });
});

function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
    const priceInput = row.querySelector('.price');
    
    const total = quantity * unitPrice;
    priceInput.value = total.toFixed(2);
}

function calculateTotals() {
    const rows = document.querySelectorAll('#itemsTable .item-row');
    let total = 0;
    
    rows.forEach(row => {
        const price = parseFloat(row.querySelector('.price').value) || 0;
        total += price;
    });
    
    document.getElementById('itemsTotal').textContent = total.toFixed(2);
}

function addMoreRow() {
    const tableBody = document.querySelector('#itemsTable tbody');
    const newRow = tableBody.querySelector('.item-row').cloneNode(true);
    
    // Clear values in the new row
    newRow.querySelector('.product-select').value = '';
    newRow.querySelector('.quantity').value = 1;
    newRow.querySelector('.unit-price').value = 0;
    newRow.querySelector('.price').value = '';
    
    tableBody.appendChild(newRow);
}

function removeRow(button) {
    const row = button.closest('tr');
    if (document.querySelectorAll('#itemsTable .item-row').length > 1) {
        row.remove();
        calculateTotals();
    } else {
        alert('You must have at least one item.');
    }
}
  </script> 
@endsection
