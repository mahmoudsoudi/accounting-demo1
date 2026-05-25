@extends('layouts.app')

@section('title', __('New Client'))

@section('content')
<div class="mb-4">
    <a href="{{ route('clients.index') }}" class="text-decoration-none">&larr; {{ __('Back to clients') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Client') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            @include('clients._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
