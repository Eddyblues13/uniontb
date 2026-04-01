@extends('errors.layout')

@section('title', '419 Session Expired')

@section('content')
<div class="error-icon">&#9203;</div>
<div class="error-code">419</div>
<h1 class="error-title">Session Expired</h1>
<p class="error-message">
    Your session has expired due to inactivity. Please refresh the page and try again. For your security, sessions
    expire after a period of inactivity.
</p>
@endsection