@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1>Create New Account</h1>
        <p class="text-muted">Add a new account to your chart of accounts</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('accounting.accounts.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="code" class="form-label">Account Code</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                @error('code')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Account Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Account Type</label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">-- Select Type --</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(old('type') == $type)>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">Parent Account (Optional)</label>
                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                    <option value="">-- No Parent --</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>
                            {{ $parent->code }} - {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Account</button>
                <a href="{{ route('accounting.accounts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
