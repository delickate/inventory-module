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
    <form action="{{ route('products.saving') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Categories:</strong><select class='form-control' name = 'form_category_id' id = 'form_category_id'> <?php if($categories) { foreach($categories as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($products->category_id) && $products->category_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_category_id', $categories, (isset($products->category_id)?$products->category_id:null), ['class' => 'some_css_class']) !!}  --></div></div>

<div class='row'>

	<div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Units:</strong><select class='form-control' name = 'form_unit_id' id = 'form_unit_id'> <?php if($units) { foreach($units as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($products->unit_id) && $products->unit_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_unit_id', $units, (isset($products->unit_id)?$products->unit_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>


<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Name:</strong>
<input type='text' name='form_name' value='{{ old('form_name') }}' class='form-control' placeholder='Name'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Sku (Stock Keeping Unit):</strong>
<input type='text' name='form_sku' value='{{ old('form_sku') }}' class='form-control' placeholder='SKU'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Barcode:</strong>
<input type='text' name='form_barcode' value='{{ old('form_barcode') }}' class='form-control' placeholder='Barcode'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Cost price:</strong>
<input type='number' name='form_cost_price' min="0"  value='{{ old('form_cost_price') }}' class='form-control' placeholder='Cost price'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Sale price:</strong>
<input type='number' name='form_sale_price' min="0" value='{{ old('form_sale_price') }}' class='form-control' placeholder='Sale price'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Description:</strong>
<input type='text' name='form_description' value='{{ old('form_description') }}' class='form-control' placeholder='form_description'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Image:</strong>
<input type="file" name="form_image" />
</div>
</div>
			
		

			
		


<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('products.listing') }}" class='btn btn-primary'>Back</a>

</div>
</form>
			</div>
             </div>
          </div>
        </div>
     </div>
  </div> 
@endsection
