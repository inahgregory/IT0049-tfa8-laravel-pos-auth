@extends('layouts.app')
@section('title', 'User Details')
@section('content')
<div class="page-heading compact"><a class="text-link" href="{{ route('users.index') }}">&larr; User accounts</a><p class="eyebrow form-eyebrow">USER DETAILS</p><h1>{{ $user->full_name }}</h1></div>
<section class="card detail-card"><img class="avatar-preview" src="{{ $user->avatar_url }}" alt="Profile picture for {{ $user->full_name }}" width="96" height="96"><dl><dt>Username</dt><dd>{{ $user->username }}</dd><dt>Email</dt><dd>{{ $user->email }}</dd><dt>Role</dt><dd>{{ $user->role }}</dd><dt>Status</dt><dd>{{ $user->status }}</dd></dl><a class="button" href="{{ route('users.edit', $user) }}">Edit user</a></section>
@if(auth()->id() !== $user->id)
<details class="danger-zone"><summary>Delete user account</summary><p>This permanently removes this user record and its prepared avatar.</p><form action="{{ route('users.destroy', $user) }}" method="POST">@csrf @method('DELETE')<label class="check-label"><input type="checkbox" name="confirm_delete" value="1" required> I confirm that I want to delete this user.</label><button class="button danger-button">Delete user</button></form></details>
@else
<p class="auth-note">To delete your own account, use <a href="{{ route('profile.edit') }}">your profile settings</a>.</p>
@endif
@include('partials.form-errors')
@endsection
