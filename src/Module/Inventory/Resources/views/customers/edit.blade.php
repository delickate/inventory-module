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
            <form action="{{ route('customers.updating',['id' => $customers->id]) }}" method="POST"  enctype="multipart/form-data">
@csrf

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Name:</strong>
<input type='text' name='form_name' value='{{ $customers->name }}' class='form-control' placeholder='form_name'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Phone:</strong>
<input type='text' name='form_phone' value='{{ $customers->phone }}' class='form-control' placeholder='form_phone'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Address:</strong>
<input type='text' name='form_address' value='{{ $customers->address }}' class='form-control' placeholder='form_address'>
</div>
</div>
			
		
</div>
<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('customers.listing') }}" class='btn btn-primary'>Back</a>

</div>
</form>

                </div>
             </div>
          </div>
        </div>
     </div>
  </div>   

@endsection
