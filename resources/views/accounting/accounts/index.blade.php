@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h1>Chart of Accounts</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('accounting.accounts.create') }}" class="btn btn-primary">
            + New Account
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Normal Balance</th>
                    <th>Parent</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr>
                        <td><strong>{{ $account->code }}</strong></td>
                        <td>{{ $account->name }}</td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($account->type) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($account->normal_balance) }}</span>
                        </td>
                        <td>
                            @if ($account->parent)
                                {{ $account->parent->code }} - {{ $account->parent->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($account->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('accounting.accounts.show', $account) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('accounting.accounts.edit', $account) }}" class="btn btn-sm btn-warning">Edit</a>
                            @if ($account->is_active)
                                <form action="{{ route('accounting.accounts.destroy', $account) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No accounts found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
