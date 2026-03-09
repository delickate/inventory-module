@extends('layouts.app')

@section('content')
<h3>Edit Integration Rule</h3>

<form action="{{ route('AutoVouchingInventory.update', $entry->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Module Name</label>
        <input type="text" name="module_name" value="{{ $entry->module_name }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Action Type</label>
        <input type="text" name="action_type" value="{{ $entry->action_type }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Debit Account ID</label>
        <select name="debit_account_id" class="form-control">
            <option value="">Select Account</option>
            {!! getHierarchicalAccounts() !!}
        </select>
        
    </div>

    <div class="form-group">
        <label>Credit Account ID</label>
        <select name="credit_account_id" class="form-control">
            <option value="">Select Account</option>
            {!! getHierarchicalAccounts() !!}
        </select>
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control">{{ $entry->description }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection
