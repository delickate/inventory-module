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
                {{ $sale_returns->return_date }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Total_Amount:</strong>
                {{ $sale_returns->total_amount }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Reason:</strong>
                {{ $sale_returns->reason }}
            </div>
        </div>
			
		
  <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Sale_Id:</strong>
                {{ $sale_returns->sales->name }}
            </div>
        </div>
			
		
 <div class='row'>
        <div class='col-xs-12 col-sm-12 col-md-12'>
            <div class='form-group'>
                <strong>Customer_Id:</strong>
                {{ $sale_returns->customers->name }}
            </div>
        </div>
			
		
 

<a href="{{ route('sale_returns.listing') }}" class='btn btn-primary'>Back</a>
@endsection
