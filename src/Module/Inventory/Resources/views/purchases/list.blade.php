@extends('layouts.app')

@section('content')
<div class="section-body mt-3">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-12">

 <a href="{{ route('purchases.adding') }}" class='btn btn-success btn-sm mb-2' title='Add New '>
    <i class='fa fa-plus' aria-hidden='true'></i> Add 
</a> 

<div class="col-md-3">
    <a href="{{ asset('storage/sample_files/inventory_module/purchases/sample_file.xlsx'); }}">download sample file</a>
</div>


                        <div class="card">
                            <div class="card-body">
@if ($errors->any())
  <ul class='alert alert-danger'>
      @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
      @endforeach
  </ul>
@endif
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
 <th>supplier</th>
 <th>invoice no</th>
 <th>total amount</th>
<th>Actions</th>
     </tr>
  </thead>
<tbody>

@foreach($purchases as $item)
   <tr>
     <td>{{ $loop->iteration }}</td>
 <td>{{ optional($item->Suppliers)->name }}</td>
 <td>{{ $item->invoice_no }}</td>
 <td>{{ $item->total_amount }}</td>

     <td>
        @if($item->is_approved == 0)
         <a href="{{ route('purchases.editing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</button></a>
         
	     <a href="{{ route('purchases.deleting', ['id' => $item->id]) }}" title='Edit'  onclick='return confirm(&quot;Confirm delete?&quot;)'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Delete</button></a>


         @can('approve_purchases')
            @if(!$item->is_approved)
                <form action="{{ route('purchases.approve', ['id' => $item->id]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning btn-sm" 
                            onclick="return confirm('Are you sure you want to approve this purchase?')">
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i> Approve
                    </button>
                </form>
            @else
                <span class="badge badge-success">Approved</span>
            @endif
        @endcan

         @endif
         <a href="{{ route('purchases.showing', ['id' => $item->id]) }}" title='Edit'><button class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> show</button></a>

         @if($item->is_approved == 0)
            <a href="{{ route('purchases.returnForm', ['id' => $item->id]) }}" 
               class="btn btn-sm btn-info">
                <i class="fa fa-undo"></i> Return
            </a>
        @endif
	</td>
  </tr>
 @endforeach

</tbody> 
    
</table>

<div class='pagination-wrapper'> {!! $purchases->appends(['search' => Request::get('search')])->render() !!} </div>

                                </div>
             </div>
          </div>
        </div>
     </div>
  </div> 

@endsection	
