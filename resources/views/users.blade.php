@extends('layouts.app')
@section('title', 'User Accounts')
@section('content')
<div class="page-heading compact"><p class="eyebrow">ACCOUNT DIRECTORY</p><h1>User Accounts</h1><p class="lead">Store team members, assigned roles, and account status.</p></div>
<section class="directory" aria-label="User Accounts"><div class="table-top"><h2>All users</h2><div class="table-tools"><a class="button" href="{{ route('users.create') }}">Create user</a><span class="pill">{{ count($users) }} accounts</span></div></div><div class="table-scroll" tabindex="0" role="region" aria-label="User Accounts table"><table><caption class="sr-only">User Accounts with fictional sample records</caption><thead><tr><th scope="col">Avatar</th><th scope="col">ID</th><th scope="col">Username</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead><tbody>
@forelse ($users as $user)
<tr><td><img class="avatar" src="{{ $user->avatar_url }}" alt="Profile picture for {{ $user->full_name }}" width="48" height="48"></td><td class="muted">{{ $user['id'] }}</td><td>{{ $user['username'] }}</td><th scope="row">{{ $user['full_name'] }}</th><td>{{ $user['email'] }}</td><td>{{ $user['role'] }}</td><td><span @class(['status', 'inactive' => $user['status'] === 'Inactive'])>{{ $user['status'] }}</span></td><td><a class="text-link" href="{{ route('users.show', $user) }}">View</a> &middot; <a class="text-link" href="{{ route('users.edit', $user) }}" aria-label="Edit {{ $user['full_name'] }}">Edit &rarr;</a></td></tr>
@empty<tr><td colspan="8">No records yet. Run the database seeder.</td></tr>@endforelse
</tbody></table></div><p class="table-note">Fictional sample accounts for demonstration purposes.</p></section>
@endsection




