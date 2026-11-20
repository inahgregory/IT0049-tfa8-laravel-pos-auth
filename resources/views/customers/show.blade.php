@extends('layouts.app')
@section('title', 'Customer Details')
@section('content')
<div class="page-heading compact"><a class="text-link" href="{{ route('customers.index') }}">&larr; Customer accounts</a><p class="eyebrow form-eyebrow">CUSTOMER DETAILS</p><h1>{{ $customer->full_name }}</h1></div>
<section class="card detail-card"><dl><dt>Email</dt><dd>{{ $customer->email }}</dd><dt>Phone</dt><dd>{{ $customer->phone ?: 'Not provided' }}</dd><dt>Status</dt><dd>{{ $customer->status }}</dd><dt>Balance (PHP)</dt><dd>{{ number_format($customer->balance, 2) }}</dd></dl><a class="button" href="{{ route('customers.edit', $customer) }}">Edit customer</a></section>
<details class="danger-zone"><summary>Delete customer account</summary><p>This permanently removes this customer record.</p><form action="{{ route('customers.destroy', $customer) }}" method="POST">@csrf @method('DELETE')<label class="check-label"><input type="checkbox" name="confirm_delete" value="1" required> I confirm that I want to delete this customer.</label><button class="button danger-button">Delete customer</button></form></details>
@include('partials.form-errors')
@endsection
