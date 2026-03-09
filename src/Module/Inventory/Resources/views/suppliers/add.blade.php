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
    <form action="{{ route('suppliers.saving') }}" method="POST" enctype="multipart/form-data">
    @csrf

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Name:</strong>
<input type='text' name='form_name' value='{{ old('form_name') }}' class='form-control' placeholder='Name'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Email:</strong>
<input type='text' name='form_email' value='{{ old('form_email') }}' class='form-control' placeholder='Email'>
</div>
</div>
			
		

<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Address:</strong>
<input type='text' name='form_address' value='{{ old('form_address') }}' class='form-control' placeholder='Address'>
</div>
</div>
			
		
</div>
<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('suppliers.listing') }}" class='btn btn-primary'>Back</a>

</div>
</form>

                </div>
             </div>
          </div>
        </div>
     </div>
  </div>   

@endsection
