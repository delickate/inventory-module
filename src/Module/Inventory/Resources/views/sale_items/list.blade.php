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

 <a href="{{ route('sale_items.adding') }}" class='btn btn-success btn-sm' title='Add New '>
    <i class='fa fa-plus' aria-hidden='true'></i> Add 
</a>

<table class='table'>
  <thead>
     <tr>
        <th>#</th> 
 <th>quantity</th>
 <th>unit_price</th>
 <th>subtotal</th>
 <th>sale_id</th>
 <th>product_id</th>
<th>Actions</th>
     </tr>
  </thead>
<tbody>

@foreach($sale_items as $item)
   <tr>
     <td>{{ $loop->iteration }}</td>
 <td>{{ $item->quantity }}</td>
 <td>{{ $item->unit_price }}</td>
 <td>{{ $item->subtotal }}</td>
 <td>{{ optional($item->sales)->date }}</td>
 <td>{{ optional($item->products)->name }}</td>

     <td>
         <a href="{{ route('sale_items.editing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>
         <a href="{{ route('sale_items.showing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>
	     <a href="{{ route('sale_items.deleting', ['id' => $item->id]) }}" title='Edit'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>
	</td>
  </tr>
 @endforeach

</tbody> 
    
</table>

<div class='pagination-wrapper'> {!! $sale_items->appends(['search' => Request::get('search')])->render() !!} </div>

@endsection	
