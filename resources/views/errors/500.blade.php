@extends('errors.layout')

@section('title', '500 Server Error')

@section('content')
<div class="error-icon">&#9888;&#65039;</div>
<div class="error-code">500</div>
<h1 class="error-title">Server Error</h1>
<p class="error-message">
    Something went wrong on our end. Our team has been notified and is working to fix the issue. Please try again later.
</p>
@endsection