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

            <form action="{{ route('purchase_items.updating',['id' => $purchase_items->id]) }}" method="POST"  enctype="multipart/form-data">
@csrf

		
<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Purchases:</strong><select class='form-control' name = 'form_purchase_id' id = 'form_purchase_id'> <?php if($purchases) { foreach($purchases as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($purchase_items->purchase_id) && $purchase_items->purchase_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_purchase_id', $purchases, (isset($purchase_items->purchase_id)?$purchase_items->purchase_id:null), ['class' => 'some_css_class']) !!}  --></div></div>

<div class='row'><div class='col-xs-12 col-sm-12 col-md-12'><div class='form-group'><strong>Products:</strong><select class='form-control' name = 'form_product_id' id = 'form_product_id'><?php if($products) { foreach($products as $key => $val) { ?><option value='<?php echo $key; ?>' <?php echo ((isset($purchase_items->product_id) && $purchase_items->product_id == $key)?'selected="selected"':'') ?>><?php echo $val; ?></option><?php } } ?></select> <!-- {!! Form::select('form_product_id', $products, (isset($purchase_items->product_id)?$purchase_items->product_id:null), ['class' => 'some_css_class']) !!}  --></div></div>
</div>

 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Quantity:</strong>
<input type='text' name='form_quantity' value='{{ $purchase_items->quantity }}' class='form-control' placeholder='form_quantity'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Unit_Price:</strong>
<input type='text' name='form_unit_price' value='{{ $purchase_items->unit_price }}' class='form-control' placeholder='form_unit_price'>
</div>
</div>
			

			

<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('purchase_items.listing') }}" class='btn btn-primary'>Back</a>

</div>

</form>


              </div>
             </div>
          </div>
        </div>
     </div>
  </div>   

@endsection
