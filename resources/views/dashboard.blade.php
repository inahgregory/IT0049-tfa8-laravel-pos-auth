@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading compact"><p class="eyebrow">SIGNED IN TO COUNTER POS</p><h1>Welcome, {{ auth()->user()->full_name }}.</h1><p class="lead">You are logged in. Customer and user account management is available from your workspace.</p><span class="pill">Authenticated session</span></div>
<section class="grid"><article class="card"><span class="index">01 / CUSTOMERS</span><h2>Customer accounts</h2><p>Create, view, edit, and delete customer records.</p><a class="text-link" href="{{ route('customers.index') }}">Manage customers &rarr;</a></article><article class="card"><span class="index">02 / TEAM</span><h2>User accounts</h2><p>Manage team records and their profile pictures.</p><a class="text-link" href="{{ route('users.index') }}">Manage users &rarr;</a></article><article class="card"><span class="index">03 / YOUR ACCOUNT</span><h2>Profile and security</h2><p>Update your profile or change your password.</p><a class="text-link" href="{{ route('profile.edit') }}">Open your profile &rarr;</a></article></section>
@endsection

