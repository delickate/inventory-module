@extends('layouts.app')

@section('content')
<h3>Accounts Integration Listing</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Module</th>
            <th>Action</th>
            <th>Debit Account</th>
            <th>Credit Account</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entries as $entry)
        <tr>
            <td>{{ $entry->id }}</td>
            <td>{{ $entry->module_name }}</td>
            <td>{{ $entry->action_type }}</td>
            <td>{{ $entry->debit_account_id }}</td>
            <td>{{ $entry->credit_account_id }}</td>
            <td>{{ $entry->description }}</td>
            <td>
                <a href="{{ route('AutoVouchingInventory.edit', $entry->id) }}" class="btn btn-sm btn-primary">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
