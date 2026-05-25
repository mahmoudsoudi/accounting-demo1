@extends('layouts.app')

@section('title', __('Edit Client'))

@section('content')
<div class="mb-4">
    <a href="{{ route('clients.show', $client) }}" class="text-decoration-none">&larr; {{ __('Back to client') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Edit Client') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            @include('clients._form', ['client' => $client])

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
