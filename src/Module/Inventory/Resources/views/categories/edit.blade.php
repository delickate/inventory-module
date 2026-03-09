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
            <form action="{{ route('categories.updating',['id' => $categories->id]) }}" method="POST"  enctype="multipart/form-data">
@csrf
 <div class='row'>
<div class='col-xs-12 col-sm-12 col-md-12'>
<div class='form-group'>
<strong>Name:</strong>
<input type='text' name='form_name' value='{{ $categories->name }}' class='form-control' placeholder='form_name'>
</div>
</div>
			
		
</div>
<div class='col-xs-12 col-sm-12 col-md-12 text-center'>
<button type='submit' class='btn btn-primary'>Submit</button>
    <a href="{{ route('categories.listing') }}" class='btn btn-primary'>Back</a>

</div>

</form>

            </div>
             </div>
          </div>
        </div>
     </div>
  </div>   

@endsection
