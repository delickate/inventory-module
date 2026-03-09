@extends('layouts.app')
@section('content')


@if ($errors->any())
  <ul class='alert alert-danger'>
      @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
      @endforeach
  </ul>
@endif
 <div class='row'>
            <form action="{{ route('purchase_return_items.updating',['id' => $purchase_return_items->id]) }}" method="POST"  enctype="multipart/form-data">
@csrf

	
<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Purchase_Returns:</strong><select class='form-control' name = 'form_purchase_return_id' id = 'form_purchase_return_id'> <?php if($purchase_returns) { foreach($purchase_returns as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($purchase_return_items->purchase_return_id) && $purchase_return_items->purchase_return_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_purchase_return_id', $purchase_returns, (isset($purchase_return_items->purchase_return_id)?$purchase_return_items->purchase_return_id:null), ['class' => 'some_css_class']) !!}  --></div></div>


<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Products:</strong><select class='form-control' name = 'form_product_id' id = 'form_product_id'> <?php if($products) { foreach($products as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($purchase_return_items->product_id) && $purchase_return_items->product_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_product_id', $products, (isset($purchase_return_items->product_id)?$purchase_return_items->product_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>

 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Quantity:</strong>
<input type='text' name='form_quantity' value='{{ $purchase_return_items->quantity }}' class='form-control' placeholder='form_quantity'>
</div>
</div>
			
		
 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Unit_Price:</strong>
<input type='text' name='form_unit_price' value='{{ $purchase_return_items->unit_price }}' class='form-control' placeholder='form_unit_price'>
</div>
</div>
			
		
 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Unit_Price:</strong>
<input type='text' name='form_unit_price' value='{{ $purchase_return_items->unit_price }}' class='form-control' placeholder='form_unit_price'>
</div>
</div>
			
	
<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('purchase_return_items.listing') }}" class='btn btn-primary'>Back</a>
</form>
</div>@endsection
