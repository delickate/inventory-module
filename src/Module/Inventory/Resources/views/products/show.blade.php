@extends('layouts.app')
@section('content')
<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">
                      
                        <div class="card">
                            <div class="card-body">

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
<strong>Name:</strong>
{{ $products->name }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Sku:</strong>
{{ $products->sku }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Barcode:</strong>
{{ $products->barcode }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Cost_Price:</strong>
{{ $products->cost_price }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Sale_Price:</strong>
{{ $products->sale_price }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Description:</strong>
{{ $products->description }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Image:</strong>
{{ $products->image }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Status:</strong>
{{ $products->status }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Category:</strong>
{{ $products->categories->name }}
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Unit:</strong>
{{ $products->units->name }}
</div>
</div>
			
		
 

<a href="{{ route('products.listing') }}" class='btn btn-primary'>Back</a>
			</div>
             </div>
          </div>
        </div>
     </div>
  </div> 
@endsection
