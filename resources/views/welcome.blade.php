@extends('shopify-app::layouts.default')

@section('content')
<div class="container">
    <!-- Header -->
    <header class="navbar navbar-primary">
        <div class="navbar-brand">
            <h1>Shopify App</h1>
        </div>
        <nav>
            <ul class="navbar-list">
                
                <li><a href="{{ URL::tokenRoute('home') }}" class="button">Home</a></li>
                <li><a href="{{ URL::tokenRoute('productListing') }}" class="button">Products</a></li>
                <li><a href="{{ URL::tokenRoute('listDiscounts') }}" class="button">Discounts</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <section class="card">
        <h2>Welcome to Your Shopify App</h2>
        <hr>
        <p>Manage your products and discounts.</p>
    </section>
</div>
@endsection

@section('styles')
<!-- Include Uptown CSS -->
<style>
    .container {
        max-width: 900px;
        margin: auto;
        text-align: center;
        padding-top: 20px;
    }
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
    }
    .navbar-list {
        display: flex;
        gap: 10px;
    }
    .card {
        padding: 20px;
        margin-top: 20px;
    }
</style>
@endsection
