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
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Return_Date:</strong>
                {{ $purchase_returns->return_date }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Total_Amount:</strong>
                {{ $purchase_returns->total_amount }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reason:</strong>
                {{ $purchase_returns->reason }}
            </div>
        </div>
			
		
  <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Purchase_Id:</strong>
                {{ $purchase_returns->purchases->invoice_no }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Supplier_Id:</strong>
                {{ $purchase_returns->products->name }}
            </div>
        </div>
			
		
 

<a href="{{ route('purchase_returns.listing') }}" class='btn btn-primary'>Back</a>
@endsection
