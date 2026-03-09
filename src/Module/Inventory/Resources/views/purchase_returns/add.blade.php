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
    <form action="{{ route('purchase_returns.saving') }}" method="POST" enctype="multipart/form-data">
    @csrf


<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Purchases:</strong><select class='form-control' name = 'form_purchase_id' id = 'form_purchase_id'> <?php if($purchases) { foreach($purchases as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($purchase_returns->purchase_id) && $purchase_returns->purchase_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_purchase_id', $purchases, (isset($purchase_returns->purchase_id)?$purchase_returns->purchase_id:null), ['class' => 'some_css_class']) !!}  --></div></div>


<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Products:</strong><select class='form-control' name = 'form_supplier_id' id = 'form_supplier_id'> <?php if($products) { foreach($products as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($purchase_returns->supplier_id) && $purchase_returns->supplier_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_supplier_id', $products, (isset($purchase_returns->supplier_id)?$purchase_returns->supplier_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>

 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Return_Date:</strong>
<input type='text' name='form_return_date' value='{{ old('form_return_date') }}' class='form-control' placeholder='form_return_date'>
</div>
</div>
			
		
 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Total_Amount:</strong>
<input type='text' name='form_total_amount' value='{{ old('form_total_amount') }}' class='form-control' placeholder='form_total_amount'>
</div>
</div>
			
		
 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Reason:</strong>
<input type='text' name='form_reason' value='{{ old('form_reason') }}' class='form-control' placeholder='form_reason'>
</div>
</div>
			

<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('purchase_returns.listing') }}" class='btn btn-primary'>Back</a>
</form>
</div>
@endsection
