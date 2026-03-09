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
                {{ $stock_movements->quantity }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Type:</strong>
                {{ $stock_movements->type }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reason:</strong>
                {{ $stock_movements->reason }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reference_Id:</strong>
                {{ $stock_movements->reference_id }}
            </div>
        </div>
			
		
  <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Warehouse_Id:</strong>
                {{ $stock_movements->warehouses->name }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Product_Id:</strong>
                {{ $stock_movements->products->name }}
            </div>
        </div>
			
		
 

<a href="{{ route('stock_movements.listing') }}" class='btn btn-primary'>Back</a>
@endsection
