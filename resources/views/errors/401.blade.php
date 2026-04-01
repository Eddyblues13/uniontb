@extends('errors.layout')

@section('title', '401 Unauthorized')

@section('content')
<div class="error-icon">&#128274;</div>
<div class="error-code">401</div>
<h1 class="error-title">Unauthorized</h1>
<p class="error-message">
    You need to be authenticated to access this page. Please log in to your account and try again.
</p>
@endsection