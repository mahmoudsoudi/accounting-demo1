@extends('layouts.app')

@section('title', 'New Journal Entry')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h1>Create New Journal Entry</h1>
        <p class="text-muted">Enter at least 2 lines with balanced debits and credits</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('accounting.journal-entries.store') }}" method="POST" id="journalForm">
            @csrf

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', today()->toDateString()) }}" required>
                    @error('date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Journal Lines</label>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="linesTable">
                        <thead>
                            <tr class="table-light">
                                <th>Account</th>
                                <th>Description</th>
                                <th style="width: 120px;">Debit</th>
                                <th style="width: 120px;">Credit</th>
                                <th style="width: 80px;"></th>
                            </tr>
                        </thead>
                        <tbody id="linesBody">
                            @forelse (old('lines', [[], []]) as $index => $line)
                                <tr class="line-row">
                                    <td>
                                        <select name="lines[{{ $index }}][account_id]" class="form-select form-select-sm @error("lines.{$index}.account_id") is-invalid @enderror" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" @selected(old("lines.{$index}.account_id") == $account->id)>
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="lines[{{ $index }}][description]" class="form-control form-control-sm" placeholder="Optional" value="{{ old("lines.{$index}.description") }}">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[{{ $index }}][debit]" class="form-control form-control-sm text-end" placeholder="0.00" step="0.01" value="{{ old("lines.{$index}.debit", '') }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[{{ $index }}][credit]" class="form-control form-control-sm text-end" placeholder="0.00" step="0.01" value="{{ old("lines.{$index}.credit", '') }}" min="0">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-line" onclick="removeLine(this)">×</button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="line-row">
                                    <td>
                                        <select name="lines[0][account_id]" class="form-select form-select-sm" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="lines[0][description]" class="form-control form-control-sm" placeholder="Optional"></td>
                                    <td><input type="number" name="lines[0][debit]" class="form-control form-control-sm text-end" placeholder="0.00" step="0.01" min="0"></td>
                                    <td><input type="number" name="lines[0][credit]" class="form-control form-control-sm text-end" placeholder="0.00" step="0.01" min="0"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-line" onclick="removeLine(this)">×</button></td>
                                </tr>
                                <tr class="line-row">
                                    <td>
                                        <select name="lines[1][account_id]" class="form-select form-select-sm" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="lines[1][description]" class="form-control form-control-sm" placeholder="Optional"></td>
                                    <td><input type="number" name="lines[1][debit]" class="form-control form-control-sm text-end" placeholder="0.00" step="0.01" min="0"></td>
                                    <td><input type="number" name="lines[1][credit]" class="form-control form-control-sm text-end" placeholder="0.00" step="0.01" min="0"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-line" onclick="removeLine(this)">×</button></td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                                <td class="text-end"><strong id="totalDebit">0.00</strong></td>
                                <td class="text-end"><strong id="totalCredit">0.00</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @error('lines')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addLine()">+ Add Line</button>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" name="action" value="draft" class="btn btn-secondary">Save as Draft</button>
                <button type="submit" name="action" value="post" class="btn btn-success">Save and Post</button>
                <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function addLine() {
    const tbody = document.getElementById('linesBody');
    const rowCount = tbody.querySelectorAll('.line-row').length;
    const newRow = document.createElement('tr');
    newRow.className = 'line-row';
    newRow.innerHTML = `
        <td>
            <select name="lines[${rowCount}][account_id]" class="form-select form-select-sm" required>
                <option value="">-- Select Account --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="lines[${rowCount}][description]" class="form-control form-control-sm" placeholder="Optional"></td>
        <td><input type="number" name="lines[${rowCount}][debit]" class="form-control form-control-sm text-end debit-input" placeholder="0.00" step="0.01" min="0" onchange="calculateTotals()"></td>
        <td><input type="number" name="lines[${rowCount}][credit]" class="form-control form-control-sm text-end credit-input" placeholder="0.00" step="0.01" min="0" onchange="calculateTotals()"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-line" onclick="removeLine(this)">×</button></td>
    `;
    tbody.appendChild(newRow);
}

function removeLine(btn) {
    const tbody = document.getElementById('linesBody');
    if (tbody.querySelectorAll('.line-row').length > 2) {
        btn.closest('.line-row').remove();
        calculateTotals();
    } else {
        alert('Journal entry must have at least 2 lines.');
    }
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
    document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
}

// Calculate totals on page load
document.addEventListener('DOMContentLoaded', calculateTotals);
document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
    input.addEventListener('change', calculateTotals);
});
</script>
@endsection
