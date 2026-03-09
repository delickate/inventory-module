@extends('layouts.app')

@section('content')


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

 <a href="{{ route('purchase_returns.adding') }}" class='btn btn-success btn-sm' title='Add New '>
    <i class='fa fa-plus' aria-hidden='true'></i> Add 
</a>

<div class="col-md-3">
    <a href="{{ asset('storage/sample_files/inventory_module/purchase_returns/sample_file.xlsx'); }}">download sample file</a>
</div>


<table class='table'>
  <thead>
     <tr>
        <th>#</th> 
 <th>return_date</th>
 <th>total_amount</th>
 <th>reason</th>
 <th>purchase_id</th>
 <th>supplier_id</th>
<th>Actions</th>
     </tr>
  </thead>
<tbody>

@foreach($purchase_returns as $item)
   <tr>
     <td>{{ $loop->iteration }}</td>
 <td>{{ $item->return_date }}</td>
 <td>{{ $item->total_amount }}</td>
 <td>{{ $item->reason }}</td>
 <td>{{ $item->purchase_id }}</td>
 <td>{{ $item->supplier_id }}</td>

     <td>
         <a href="{{ route('purchase_returns.editing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>
         <a href="{{ route('purchase_returns.showing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>
	     <a href="{{ route('purchase_returns.deleting', ['id' => $item->id]) }}" title='Edit'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>
	</td>
  </tr>
 @endforeach

</tbody> 
    
</table>

<div class='pagination-wrapper'> {!! $purchase_returns->appends(['search' => Request::get('search')])->render() !!} </div>

@endsection	
