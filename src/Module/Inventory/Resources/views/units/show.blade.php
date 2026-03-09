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
{{ $units->name }}
</div>
</div>
			
		
  

<a href="{{ route('units.listing') }}" class='btn btn-primary'>Back</a>


            </div>
             </div>
          </div>
        </div>
     </div>
  </div>   
  
@endsection
