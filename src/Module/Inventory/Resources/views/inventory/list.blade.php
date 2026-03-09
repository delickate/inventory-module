@extends('layouts.app')

@section('content')
<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">

                        
                        <div class="card">
                            <div class="card-body">
<div class="col-md-3">
    <a href="{{ asset('storage/sample_files/inventory_module/inventory/sample_file.xlsx'); }}">download sample file</a>
</div>

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

<!--  <a href=" route  ('inventory.adding') " class='btn btn-success btn-sm' title='Add New '>
    <i class='fa fa-plus' aria-hidden='true'></i> Add 
</a> -->

<table class='table'>
  <thead>
     <tr>
        <th>#</th> 
 <th>quantity</th>
 <th>warehouse</th>
 <th>product</th>
<!-- <th>Actions</th> -->
     </tr>
  </thead>
<tbody>

@foreach($inventory as $item)
   <tr>
     <td>{{ $loop->iteration }}</td>
 <td>{{ $item->quantity }}</td>
 <td>{{ optional($item->Warehouses)->name }}</td>
 <td>{{ optional($item->Products)->name }}</td>

     <!-- <td>
         <a href="{{ route('inventory.editing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>
         <a href="{{ route('inventory.showing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>
	     <a href="{{ route('inventory.deleting', ['id' => $item->id]) }}" title='Edit'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a> -->
	</td>
  </tr>
 @endforeach

</tbody> 
    
</table>

<div class='pagination-wrapper'> {!! $inventory->appends(['search' => Request::get('search')])->render() !!} </div>
               </div>
             </div>
          </div>
        </div>
     </div>
  </div>   
@endsection	
