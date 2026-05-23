@extends('layouts.app')

@section('title', __('Edit User'))

@section('content')
<div class="mb-4">
    <a href="{{ route('users.index') }}" class="text-decoration-none">&larr; {{ __('Back to users') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Edit User') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">{{ __('New password') }}</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    <small class="text-muted">{{ __('Leave blank to keep current.') }}</small>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="role_id">{{ __('Role') }}</label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                               @checked(old('is_active', $user->is_active))>
                        <label class="form-check-label" for="is_active">{{ __('Active account') }}</label>
                    </div>
                    @error('is_active')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Update user') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
