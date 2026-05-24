@extends('layouts.app')

@section('title', __('Login'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">{{ __('Sign In') }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email / Username') }}</label>
                        <input type="text" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Password') }}</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">{{ __('Remember me') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('Login') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
