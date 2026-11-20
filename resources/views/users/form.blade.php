@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Create User')
@section('content')
<div class="page-heading compact"><a class="text-link" href="{{ route('users.index') }}">&larr; User accounts</a><p class="eyebrow form-eyebrow">ACCOUNT MANAGEMENT</p><h1>{{ $user->exists ? 'Edit User' : 'Create User' }}</h1><p class="lead">{{ $user->exists ? 'Update team details and add a profile picture.' : 'Create a new account for a member of your store team.' }}</p></div>
@include('partials.form-errors')
<form class="account-form" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST" @if($user->exists) enctype="multipart/form-data" @endif>
    @csrf
    @if ($user->exists) @method('PUT') @endif
    <div class="form-header"><h2>User details</h2><p>Fields marked * are required.</p></div>
    <div class="form-grid">
        <div class="field"><label for="username">Username *</label><input id="username" name="username" value="{{ old('username', $user->username) }}" maxlength="50" required autocomplete="username" @error('username') aria-invalid="true" aria-describedby="username-error" @enderror>@error('username')<p class="field-error" id="username-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="full_name">Full name *</label><input id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" maxlength="255" required autocomplete="name" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>@error('full_name')<p class="field-error" id="full_name-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="email">Email *</label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required autocomplete="email">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="role">Role</label><select id="role" name="role">@foreach (['Administrator', 'Manager', 'Cashier', 'Inventory Clerk'] as $role)<option @selected(old('role', $user->role ?? 'Cashier') === $role)>{{ $role }}</option>@endforeach</select>@error('role')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="status">Status</label><select id="status" name="status">@foreach (['Active', 'Inactive'] as $status)<option @selected(old('status', $user->status ?? 'Active') === $status)>{{ $status }}</option>@endforeach</select>@error('status')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="password">{{ $user->exists ? 'New password (optional)' : 'Password *' }}</label><input id="password" type="password" name="password" minlength="8" autocomplete="new-password" @if(!$user->exists) required @endif><p class="field-hint">At least 8 characters. {{ $user->exists ? 'Leave blank to keep the current password.' : 'Choose a password for this user.' }}</p>@error('password')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" @if(!$user->exists) required @endif></div>
    </div>
    @if ($user->exists)
    <section class="avatar-section"><img class="avatar-preview" src="{{ $user->avatar_url }}" alt="Current profile picture for {{ $user->full_name }}" width="96" height="96"><div class="field"><label for="avatar">Profile picture</label><input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" aria-describedby="avatar-help @error('avatar') avatar-error @enderror"><p id="avatar-help" class="field-hint">JPEG, PNG, or WebP. Maximum 2 MB and 4096 &times; 4096 pixels. Saved as a 256 &times; 256 thumbnail. Leave blank to keep your current picture.</p>@error('avatar')<p class="field-error" id="avatar-error">{{ $message }}</p>@enderror<p class="field-hint">After a validation error, choose your file again before saving.</p></div></section>
    @else
    <p class="form-tip">You can add a profile picture after creating the account using its Edit button.</p>
    @endif
    <div class="form-actions"><button class="button" type="submit">{{ $user->exists ? 'Save changes' : 'Create user' }}</button><a class="button secondary" href="{{ route('users.index') }}">Cancel</a></div>
</form>
@endsection


