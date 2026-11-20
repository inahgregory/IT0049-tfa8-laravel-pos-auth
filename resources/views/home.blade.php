@extends('layouts.app')
@section('title', 'Home')
@section('content')
<section class="hero">
    <div><p class="eyebrow">YOUR STORE, CONNECTED</p><h1>Every account.<br>One clear view.</h1>
    <p class="lead">Keep customer details and store team accounts within easy reach. A simple foundation for everyday retail operations.</p>
    <div class="actions"><a class="button" href="{{ route('customers.index') }}">View customer accounts &rarr;</a><a class="button secondary" href="{{ route('users.index') }}">View user accounts</a></div></div>
    <aside class="hero-panel"><span class="eyebrow">COUNTER POS</span><div class="receipt-icon">C</div><h2>A better starting point<br>for your store.</h2><p>Customers. People. Possibilities.</p><span class="pill">Account directory</span></aside>
</section>
<section class="grid" aria-label="Explore Counter POS">
    <article class="card"><span class="index">01 / CUSTOMERS</span><h2>Know your customers</h2><p>Find contact information, account status, and sample balances in one organized directory.</p><a class="text-link" href="{{ route('customers.index') }}">Open customer accounts &rarr;</a></article>
    <article class="card"><span class="index">02 / TEAM</span><h2>Meet your store team</h2><p>Review user accounts, assigned roles, and the status of each team member.</p><a class="text-link" href="{{ route('users.index') }}">Open user accounts &rarr;</a></article>
    <article class="card"><span class="index">03 / ABOUT</span><h2>Built on clear foundations</h2><p>Learn about this project, its purpose, and the scope of the account directory.</p><a class="text-link" href="{{ route('about') }}">About the project &rarr;</a></article>
</section>
@endsection

