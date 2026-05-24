@extends('layouts.app')

@section('title', __('Edit Template'))

@section('content')
<div class="mb-4">
    <a href="{{ route('templates.show', $template) }}" class="text-decoration-none">&larr; {{ __('Back to template') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Edit Template') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('templates.update', $template) }}" method="POST">
            @csrf
            @method('PUT')
            @include('templates._form')
            <div class="mt-4 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                <a href="{{ route('templates.show', $template) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>

        @can('create-accounting')
        <form action="{{ route('templates.destroy', $template) }}" method="POST" class="mt-3"
              onsubmit="return confirm(@json(__('Delete this template? This cannot be undone.')));">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">{{ __('Delete template') }}</button>
        </form>
        @endcan
    </div>
</div>
@endsection
