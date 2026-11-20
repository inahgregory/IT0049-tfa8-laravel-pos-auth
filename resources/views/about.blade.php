@extends('layouts.app')
@section('title', 'About')
@section('content')
<div class="page-heading"><p class="eyebrow">ABOUT THE PROJECT</p><h1>A simple start for<br>smarter store operations.</h1><p class="lead">Counter POS brings customer and team account information together in a small, easy-to-navigate application.</p></div>
<section class="about-grid"><article class="card"><h2>Purpose</h2><p>This project establishes the first four pages of a point-of-sale application: Home, About, Customer Accounts, and User Accounts.</p><p>The customer directory presents contact details, account status, and sample balances. The user directory presents team member usernames, names, emails, roles, and account status.</p><h2>Current scope</h2><p>Create and edit customer and user accounts with validation. User profile pictures are prepared as thumbnails and stored on the public disk. Account management uses resource controllers and requires a logged-in session through Laravel Breeze. Checkout and payments are outside this laboratory's scope.</p></article>
<article class="card"><span class="index">LABORATORY DETAILS</span><dl><dt>Activity</dt><dd>TFA8 &middot; Resource Controllers and Authentication</dd><dt>Course</dt><dd>IT0049 &middot; Web System Technologies</dd><dt>Student</dt><dd>Inah Angela Gregorio</dd><dt>Section</dt><dd>BSITCST - AC32</dd><dt>Built with</dt><dd>Laravel, PHP, Blade, CSS, and Eloquent</dd></dl><a class="text-link" href="{{ route('customers.index') }}">Explore customer accounts &rarr;</a></article></section>
@endsection




