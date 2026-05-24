@extends('layouts.app')

@section('title', __('Transaction Templates'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">{{ __('Transaction Templates') }}</h1>
    @can('create-accounting')
        <a href="{{ route('templates.create') }}" class="btn btn-primary">{{ __('New template') }}</a>
    @endcan
</div>

<p class="text-muted mb-4">{{ __('Reusable journal entry patterns. Create your own templates or apply a built-in one to generate balanced entries quickly.') }}</p>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Lines') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                <tr>
                    <td>{{ $template->name }}</td>
                    <td><code>{{ $template->slug }}</code></td>
                    <td>{{ $template->lines_count }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge bg-success">{{ __('Yes') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('No') }}</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('templates.show', $template) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                        @can('create-accounting')
                            <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                            @if($template->is_active)
                                <a href="{{ route('templates.apply', $template) }}" class="btn btn-sm btn-primary">{{ __('Apply') }}</a>
                            @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('No templates defined.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
