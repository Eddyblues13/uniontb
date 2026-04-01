@extends('errors.layout')

@section('title', '429 Too Many Requests')

@section('content')
<div class="error-icon">&#9889;</div>
<div class="error-code">429</div>
<h1 class="error-title">Too Many Requests</h1>
<p class="error-message">
    You have made too many requests in a short period. Please wait a moment and try again.
</p>
@endsection