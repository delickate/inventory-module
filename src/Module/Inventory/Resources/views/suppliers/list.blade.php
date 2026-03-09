@extends('layouts.app')

@section('content')
<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">

                        <a href="{{ route('suppliers.adding') }}" class='btn btn-success btn-sm mb-2' title='Add New '>
    <i class='fa fa-plus' aria-hidden='true'></i> Add 
</a>

<div class="col-md-3">
    <a href="{{ asset('storage/sample_files/inventory_module/suppliers/sample_file.xlsx'); }}">download sample file</a>
</div>


                        <div class="card">
                            <div class="card-body">

<?php if(Session::has('success_message')){ ?>
  <div class='alert alert-success alert-dismissable text-left'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <i class='icon fa fa-check'></i>Success: <?php echo Session::get('success_message');?>
  </div>

<?php }elseif(Session::has('error_message')){ ?>
  <div class='alert alert-danger alert-dismissable text-left'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <i class='icon fa fa-ban'></i>Error: <?php echo Session::get('error_message');?>
  </div> 
<?php } ?>

 <h3>{{ $title }}</h3>

<table class='table'>
  <thead>
     <tr>
        <th>#</th> 
 <th>name</th>
 <th>email</th>
 <th>address</th>
<th>Actions</th>
     </tr>
  </thead>
<tbody>

@foreach($suppliers as $item)
   <tr>
     <td>{{ $loop->iteration }}</td>
 <td>{{ $item->name }}</td>
 <td>{{ $item->email }}</td>
 <td>{{ $item->address }}</td>

     <td>
         <a href="{{ route('suppliers.editing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-warning btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>
         <a href="{{ route('suppliers.showing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>
	     <a href="{{ route('suppliers.deleting', ['id' => $item->id]) }}" title='Edit'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-danger btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>
	</td>
  </tr>
 @endforeach

</tbody> 
    
</table>

<div class='pagination-wrapper'> {!! $suppliers->appends(['search' => Request::get('search')])->render() !!} </div>
                    
                    </div>
             </div>
          </div>
        </div>
     </div>
  </div>   

@endsection	
