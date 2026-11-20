<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Counter POS account management with Laravel Breeze authentication.">
<title>@yield('title') | Counter POS</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="header"><div class="bar">
<a class="brand" href="{{ route('home') }}"><span class="mark">C</span> Counter<span class="muted">POS</span></a>
<nav aria-label="Main navigation">
@foreach (['home' => 'Home', 'about' => 'About', 'customers.index' => 'Customers', 'users.index' => 'Users'] as $route => $label)
<a href="{{ route($route) }}" @class(['nav-link', 'current' => request()->routeIs($route === 'customers.index' ? 'customers.*' : ($route === 'users.index' ? 'users.*' : $route))])>{{ $label }}</a>
@endforeach
@auth
<a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
<form action="{{ route('logout') }}" method="POST" class="logout-form">@csrf<button class="nav-link logout-button" type="submit">Log out</button></form>
@else
<a class="nav-link" href="{{ route('login') }}">Log in</a>
<a class="nav-link" href="{{ route('register') }}">Register</a>
@endauth
</nav></div></header>
<main id="main" class="container">
@if (session('success'))
<div class="success-message" role="status">{{ session('success') }}</div>
@endif
@yield('content')
</main>
<footer class="footer"><div class="bar"><span>Counter POS &middot; Secure account directory</span><span>Inah Angela Gregorio &middot; BSITCST - AC32</span></div></footer>
</body></html>

