@extends('layouts.app')

@section('title', __('Users'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Users') }}</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('New User') }}</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->role?->name }}</td>
                    <td>
                        @if($u->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
