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
                {{ $inventory->quantity }}
            </div>
        </div>
			
		
  <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Warehouse_Id:</strong>
                {{ $inventory->warehouses->name }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Product_Id:</strong>
                {{ $inventory->products->name }}
            </div>
        </div>
			
		
 

<a href="{{ route('inventory.listing') }}" class='btn btn-primary'>Back</a>
@endsection
