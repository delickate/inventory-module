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
            <form action="{{ route('sale_returns.updating',['id' => $sale_returns->id]) }}" method="POST"  enctype="multipart/form-data">
        @csrf
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Return_Date:</strong>
                <input type='text' name='form_return_date' value='{{ $sale_returns->return_date }}' class='form-control' placeholder='form_return_date'>
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Total_Amount:</strong>
                <input type='text' name='form_total_amount' value='{{ $sale_returns->total_amount }}' class='form-control' placeholder='form_total_amount'>
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reason:</strong>
                <input type='text' name='form_reason' value='{{ $sale_returns->reason }}' class='form-control' placeholder='form_reason'>
            </div>
        </div>
			
		
<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Sales:</strong><select class='form-control' name = 'form_sale_id' id = 'form_sale_id'> <option value=''></option><?php if($sales) { foreach($sales as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($sale_returns->sale_id) && $sale_returns->sale_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_sale_id', $sales, (isset($sale_returns->sale_id)?$sale_returns->sale_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Customers:</strong><select class='form-control' name = 'form_customer_id' id = 'form_customer_id'> <option value=''></option><?php if($customers) { foreach($customers as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($sale_returns->customer_id) && $sale_returns->customer_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_customer_id', $customers, (isset($sale_returns->customer_id)?$sale_returns->customer_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>
<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
     <button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('sale_returns.listing') }}" class='btn btn-primary'>Back</a>
</form>
</div>@endsection
