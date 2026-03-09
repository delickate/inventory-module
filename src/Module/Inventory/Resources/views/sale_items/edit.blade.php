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
            <form action="{{ route('sale_items.updating',['id' => $sale_items->id]) }}" method="POST"  enctype="multipart/form-data">
@csrf

<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Sales:</strong><select class='form-control' name = 'form_sale_id' id = 'form_sale_id'> <?php if($sales) { foreach($sales as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($sale_items->sale_id) && $sale_items->sale_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_sale_id', $sales, (isset($sale_items->sale_id)?$sale_items->sale_id:null), ['class' => 'some_css_class']) !!}  --></div></div>


<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Products:</strong><select class='form-control' name = 'form_product_id' id = 'form_product_id'><?php if($products) { foreach($products as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($sale_items->product_id) && $sale_items->product_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_product_id', $products, (isset($sale_items->product_id)?$sale_items->product_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>

 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Quantity:</strong>
<input type='text' name='form_quantity' value='{{ $sale_items->quantity }}' class='form-control' placeholder='form_quantity'>
</div>
</div>
			
		
 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Unit_Price:</strong>
<input type='text' name='form_unit_price' value='{{ $sale_items->unit_price }}' class='form-control' placeholder='form_unit_price'>
</div>
</div>
			
		
 <!-- <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Subtotal:</strong>
<input type='text' name='form_subtotal' value='{{ $sale_items->subtotal }}' class='form-control' placeholder='form_subtotal'>
</div>
</div>
			 -->
		

<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('sale_items.listing') }}" class='btn btn-primary'>Back</a>
</form>
</div>@endsection
