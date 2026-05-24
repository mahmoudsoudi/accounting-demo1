@extends('layouts.app')

@section('title', __('Edit Account'))

@section('content')
<div class="mb-4">
    <a href="{{ route('accounts.index') }}" class="text-decoration-none">&larr; {{ __('Back to accounts') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Edit Account') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('accounts.update', $account) }}" method="POST">
            @csrf
            @method('PUT')
            @include('accounts._form', ['account' => $account])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
