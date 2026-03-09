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
                <strong>Quantity:</strong>
                {{ $purchase_return_items->quantity }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Unit_Price:</strong>
                {{ $purchase_return_items->unit_price }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Unit_Price:</strong>
                {{ $purchase_return_items->unit_price }}
            </div>
        </div>
			
		
  <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Purchase_Return_Id:</strong>
                {{ $purchase_return_items->purchase_returns->invoice_no }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Product_Id:</strong>
                {{ $purchase_return_items->products->name }}
            </div>
        </div>
			
		
 

<a href="{{ route('purchase_return_items.listing') }}" class='btn btn-primary'>Back</a>
@endsection
