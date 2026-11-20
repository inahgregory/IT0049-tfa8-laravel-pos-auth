@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('content')
<div class="page-heading compact"><p class="eyebrow">ACCOUNT DIRECTORY</p><h1>Customer Accounts</h1><p class="lead">Customer contact details, account status, and outstanding sample balances.</p></div>
<section class="directory" aria-label="Customer Accounts"><div class="table-top"><h2>All customers</h2><div class="table-tools"><a class="button" href="{{ route('customers.create') }}">Create customer</a><span class="pill">{{ count($customers) }} accounts</span></div></div><div class="table-scroll" tabindex="0" role="region" aria-label="Customer Accounts table"><table><caption class="sr-only">Customer Accounts with fictional sample records</caption><thead><tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Status</th><th scope="col">Balance (PHP)</th><th scope="col">Actions</th></tr></thead><tbody>
@forelse ($customers as $customer)
<tr><td class="muted">{{ $customer['id'] }}</td><th scope="row">{{ $customer['full_name'] }}</th><td>{{ $customer['email'] }}</td><td>{{ $customer['phone'] }}</td><td><span @class(['status', 'inactive' => $customer['status'] === 'Inactive'])>{{ $customer['status'] }}</span></td><td class="number">{{ number_format($customer['balance'], 2) }}</td><td><a class="text-link" href="{{ route('customers.show', $customer) }}">View</a> &middot; <a class="text-link" href="{{ route('customers.edit', $customer) }}" aria-label="Edit {{ $customer['full_name'] }}">Edit &rarr;</a></td></tr>
@empty<tr><td colspan="7">No records yet. Run the database seeder.</td></tr>@endforelse
</tbody></table></div><p class="table-note">Fictional sample accounts for demonstration purposes.</p></section>
@endsection




