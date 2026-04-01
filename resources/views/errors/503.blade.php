@extends('errors.layout')

@section('title', '503 Service Unavailable')

@section('content')
<div class="error-icon">&#128736;&#65039;</div>
<div class="error-code">503</div>
<h1 class="error-title">Service Unavailable</h1>
<p class="error-message">
    We are currently performing scheduled maintenance. We will be back online shortly. Thank you for your patience.
</p>
@endsection