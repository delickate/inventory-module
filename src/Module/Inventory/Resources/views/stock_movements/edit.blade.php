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
            <form action="{{ route('stock_movements.updating',['id' => $stock_movements->id]) }}" method="POST"  enctype="multipart/form-data">
        @csrf
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Quantity:</strong>
                <input type='text' name='form_quantity' value='{{ $stock_movements->quantity }}' class='form-control' placeholder='form_quantity'>
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Type:</strong>
                <input type='text' name='form_type' value='{{ $stock_movements->type }}' class='form-control' placeholder='form_type'>
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reason:</strong>
                <input type='text' name='form_reason' value='{{ $stock_movements->reason }}' class='form-control' placeholder='form_reason'>
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reference_Id:</strong>
                <input type='text' name='form_reference_id' value='{{ $stock_movements->reference_id }}' class='form-control' placeholder='form_reference_id'>
            </div>
        </div>
			
		
<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Warehouses:</strong><select class='form-control' name = 'form_warehouse_id' id = 'form_warehouse_id'> <option value=''></option><?php if($warehouses) { foreach($warehouses as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($stock_movements->warehouse_id) && $stock_movements->warehouse_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_warehouse_id', $warehouses, (isset($stock_movements->warehouse_id)?$stock_movements->warehouse_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Products:</strong><select class='form-control' name = 'form_product_id' id = 'form_product_id'> <option value=''></option><?php if($products) { foreach($products as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($stock_movements->product_id) && $stock_movements->product_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_product_id', $products, (isset($stock_movements->product_id)?$stock_movements->product_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>
<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
     <button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('stock_movements.listing') }}" class='btn btn-primary'>Back</a>
</form>
</div>@endsection
