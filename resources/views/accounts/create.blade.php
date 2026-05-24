@extends('layouts.app')

@section('title', __('New Account'))

@section('content')
<div class="mb-4">
    <a href="{{ route('accounts.index') }}" class="text-decoration-none">&larr; {{ __('Back to accounts') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Account') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf
            @include('accounts._form', ['account' => null])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
