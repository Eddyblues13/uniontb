@extends('errors.layout')

@section('title', '403 Forbidden')

@section('content')
<div class="error-icon">&#128683;</div>
<div class="error-code">403</div>
<h1 class="error-title">Access Denied</h1>
<p class="error-message">
    {{ $exception->getMessage() ?: 'You do not have permission to access this page. If you believe this is an error,
    please contact our support team.' }}
</p>
@endsection