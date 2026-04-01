@extends('errors.layout')

@section('title', '404 Page Not Found')

@section('content')
<div class="error-icon">&#128270;</div>
<div class="error-code">404</div>
<h1 class="error-title">Page Not Found</h1>
<p class="error-message">
    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
</p>
@endsection