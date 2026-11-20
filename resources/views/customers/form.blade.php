@extends('layouts.app')
@section('title', $customer->exists ? 'Edit Customer' : 'Create Customer')
@section('content')
<div class="page-heading compact"><a class="text-link" href="{{ route('customers.index') }}">&larr; Customer accounts</a><p class="eyebrow form-eyebrow">ACCOUNT MANAGEMENT</p><h1>{{ $customer->exists ? 'Edit Customer' : 'Create Customer' }}</h1><p class="lead">Keep customer contact details and account information up to date.</p></div>
@include('partials.form-errors')
<form class="account-form" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}" method="POST">
    @csrf
    @if ($customer->exists) @method('PUT') @endif
    <div class="form-header"><h2>Customer details</h2><p>Fields marked * are required.</p></div>
    <div class="form-grid">
        <div class="field"><label for="full_name">Full name *</label><input id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name) }}" maxlength="255" required autocomplete="name" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>@error('full_name')<p class="field-error" id="full_name-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="email">Email *</label><input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" maxlength="255" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>@error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="phone">Phone</label><input type="tel" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="40" autocomplete="tel">@error('phone')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="status">Status</label><select id="status" name="status">@foreach (['Active', 'Inactive'] as $status)<option @selected(old('status', $customer->status ?? 'Active') === $status)>{{ $status }}</option>@endforeach</select>@error('status')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="balance">Balance (PHP)</label><input type="number" id="balance" name="balance" min="0" max="9999999999.99" step="0.01" value="{{ old('balance', $customer->balance ?? 0) }}">@error('balance')<p class="field-error">{{ $message }}</p>@enderror</div>
    </div>
    <div class="form-actions"><button class="button" type="submit">{{ $customer->exists ? 'Save changes' : 'Create customer' }}</button><a class="button secondary" href="{{ route('customers.index') }}">Cancel</a></div>
</form>
@endsection

