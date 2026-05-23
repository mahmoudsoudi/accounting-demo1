@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('templates.index') }}" class="text-decoration-none">&larr; {{ __('Back to templates') }}</a>
</div>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-2">{{ $template->name }}</h1>
        <p class="text-muted mb-1"><code>{{ $template->slug }}</code></p>
        @if($template->description)
            <p class="mb-0">{{ $template->description }}</p>
        @endif
    </div>
    @can('create-accounting')
        @if($template->is_active)
            <a href="{{ route('templates.apply', $template) }}" class="btn btn-primary">{{ __('Apply template') }}</a>
        @endif
    @endcan
</div>

<div class="card mb-4">
    <div class="card-header">{{ __('Template variables') }}</div>
    <div class="card-body">
        @if(is_array($template->variables) && count($template->variables))
            <ul class="mb-0">
                @foreach($template->variables as $key => $meta)
                    <li>{{ is_string($key) ? $key : ($meta['name'] ?? json_encode($meta)) }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-muted mb-0">{{ __('No variable metadata.') }}</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">{{ __('Lines') }}</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Account code') }}</th>
                    <th>{{ __('Side') }}</th>
                    <th>{{ __('Amount variable') }}</th>
                    <th>{{ __('Description template') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($template->lines as $line)
                <tr>
                    <td>{{ $line->line_order }}</td>
                    <td><code>{{ $line->account_code }}</code></td>
                    <td class="text-capitalize">{{ $line->side }}</td>
                    <td>{{ $line->amount_variable ?? '—' }}</td>
                    <td>{{ $line->description_template ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
