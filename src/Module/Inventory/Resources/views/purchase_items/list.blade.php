@extends('layouts.app')

@section('content')

<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">

<div class="col-md-3">
    <a href="{{ asset('storage/sample_files/inventory_module/purchase_items/sample_file.xlsx'); }}">download sample file</a>
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

 <a href="{{ route('purchase_items.adding') }}" class='btn btn-success btn-sm' title='Add New '>
    <i class='fa fa-plus' aria-hidden='true'></i> Add 
</a>

<table class='table'>
  <thead>
     <tr>
        <th>#</th> 
 <th>purchase</th>
 <th>product</th>
 <th>quantity</th>
 <th>unit price</th>
 <th>subtotal</th>
<th>Actions</th>
     </tr>
  </thead>
<tbody>

@foreach($purchase_items as $item)
   <tr>
     <td>{{ $loop->iteration }}</td>
 <td>{{ optional($item->Purchases)->name }}</td>
 <td>{{ optional($item->Products)->name }}</td>
 <td>{{ $item->quantity }}</td>
 <td>{{ $item->unit_price }}</td>
 <td>{{ $item->subtotal }}</td>

     <td>
         <a href="{{ route('purchase_items.editing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>
         <a href="{{ route('purchase_items.showing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>
	     <a href="{{ route('purchase_items.deleting', ['id' => $item->id]) }}" title='Edit'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>
	</td>
  </tr>
 @endforeach

</tbody> 
    
</table>

<div class='pagination-wrapper'> {!! $purchase_items->appends(['search' => Request::get('search')])->render() !!} </div>

                </div>
             </div>
          </div>
        </div>
     </div>
  </div>   

@endsection	
