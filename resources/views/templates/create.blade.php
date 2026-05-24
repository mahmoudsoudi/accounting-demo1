@extends('layouts.app')

@section('title', __('New Transaction Template'))

@section('content')
<div class="mb-4">
    <a href="{{ route('templates.index') }}" class="text-decoration-none">&larr; {{ __('Back to templates') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Transaction Template') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('templates.store') }}" method="POST">
            @csrf
            @include('templates._form', ['template' => null])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save template') }}</button>
                <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
