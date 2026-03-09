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
            <form action="{{ route('inventory.updating',['id' => $inventory->id]) }}" method="POST"  enctype="multipart/form-data">
@csrf

<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Warehouses:</strong><select class='form-control' name = 'form_warehouse_id' id = 'form_warehouse_id'> <?php if($warehouses) { foreach($warehouses as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($inventory->warehouse_id) && $inventory->warehouse_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_warehouse_id', $warehouses, (isset($inventory->warehouse_id)?$inventory->warehouse_id:null), ['class' => 'some_css_class']) !!}  --></div></div>


<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Products:</strong><select class='form-control' name = 'form_product_id' id = 'form_product_id'> <?php if($products) { foreach($products as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($inventory->product_id) && $inventory->product_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_product_id', $products, (isset($inventory->product_id)?$inventory->product_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>

 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Quantity:</strong>
<input type='text' name='form_quantity' value='{{ $inventory->quantity }}' class='form-control' placeholder='form_quantity'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('inventory.listing') }}" class='btn btn-primary'>Back</a>
</form>
</div>@endsection
