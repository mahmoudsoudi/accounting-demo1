@php
    $menuPrefix = $menuPrefix ?? '';
    $dismissOffcanvas = ! empty($dismissOffcanvas);
    $offcanvasDismiss = $dismissOffcanvas ? 'data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas"' : '';
@endphp
<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" {!! $offcanvasDismiss !!}>{{ __('Dashboard') }}</a>

    <small class="text-secondary text-uppercase mt-3 mb-1">{{ __('Accounting') }}</small>
    <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}" {!! $offcanvasDismiss !!}>{{ __('Chart of Accounts') }}</a>
    <a class="nav-link {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}" href="{{ route('journal-entries.index') }}" {!! $offcanvasDismiss !!}>{{ __('Journal Entries') }}</a>
    <a class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" href="{{ route('templates.index') }}" {!! $offcanvasDismiss !!}>{{ __('Transaction Templates') }}</a>
    <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}" {!! $offcanvasDismiss !!}>{{ __('Invoices') }}</a>
    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}" {!! $offcanvasDismiss !!}>{{ __('Expenses') }}</a>
    <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}" {!! $offcanvasDismiss !!}>{{ __('Payments') }}</a>
    <a class="nav-link {{ request()->routeIs('trust-transactions.*') ? 'active' : '' }}" href="{{ route('trust-transactions.index') }}" {!! $offcanvasDismiss !!}>{{ __('Trust Transactions') }}</a>

    <small class="text-secondary text-uppercase mt-3 mb-1">{{ __('Real Estate') }}</small>
    <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}" {!! $offcanvasDismiss !!}>{{ __('Clients') }}</a>
    <a class="nav-link {{ request()->routeIs('agents.*') ? 'active' : '' }}" href="{{ route('agents.index') }}" {!! $offcanvasDismiss !!}>{{ __('Agents') }}</a>
    <a class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}" href="{{ route('properties.index') }}" {!! $offcanvasDismiss !!}>{{ __('Properties') }}</a>

    <small class="text-secondary text-uppercase mt-3 mb-1">{{ __('Tools') }}</small>

    @php($reportsOpen = request()->routeIs('reports.*'))
    <a class="nav-link sidebar-toggle {{ $reportsOpen ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#reportsSubmenu{{ $menuPrefix }}"
       role="button"
       aria-expanded="{{ $reportsOpen ? 'true' : 'false' }}"
       aria-controls="reportsSubmenu{{ $menuPrefix }}">
        {{ __('Reports') }}
        <span class="sidebar-chevron">&rsaquo;</span>
    </a>
    <div class="collapse {{ $reportsOpen ? 'show' : '' }}" id="reportsSubmenu{{ $menuPrefix }}">
        <nav class="nav flex-column sidebar-submenu">
            <a class="nav-link {{ request()->routeIs('reports.trial-balance') ? 'active' : '' }}" href="{{ route('reports.trial-balance') }}" {!! $offcanvasDismiss !!}>{{ __('Trial Balance') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}" href="{{ route('reports.balance-sheet') }}" {!! $offcanvasDismiss !!}>{{ __('Balance Sheet') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.income-statement') ? 'active' : '' }}" href="{{ route('reports.income-statement') }}" {!! $offcanvasDismiss !!}>{{ __('Income Statement') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.cash-flow') ? 'active' : '' }}" href="{{ route('reports.cash-flow') }}" {!! $offcanvasDismiss !!}>{{ __('Cash Flow') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.general-ledger') ? 'active' : '' }}" href="{{ route('reports.general-ledger') }}" {!! $offcanvasDismiss !!}>{{ __('General Ledger') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.journal-register') ? 'active' : '' }}" href="{{ route('reports.journal-register') }}" {!! $offcanvasDismiss !!}>{{ __('Journal Register') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.ar-aging') ? 'active' : '' }}" href="{{ route('reports.ar-aging') }}" {!! $offcanvasDismiss !!}>{{ __('A/R Aging') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.expense-summary') ? 'active' : '' }}" href="{{ route('reports.expense-summary') }}" {!! $offcanvasDismiss !!}>{{ __('Expense Summary') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.revenue-by-dimension') ? 'active' : '' }}" href="{{ route('reports.revenue-by-dimension') }}" {!! $offcanvasDismiss !!}>{{ __('Revenue by Dimension') }}</a>
            <a class="nav-link {{ request()->routeIs('reports.trust-fund') ? 'active' : '' }}" href="{{ route('reports.trust-fund') }}" {!! $offcanvasDismiss !!}>{{ __('Trust Fund Report') }}</a>
        </nav>
    </div>

    @php($calculatorsOpen = request()->routeIs('calculators.*'))
    <a class="nav-link sidebar-toggle {{ $calculatorsOpen ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#calculatorsSubmenu{{ $menuPrefix }}"
       role="button"
       aria-expanded="{{ $calculatorsOpen ? 'true' : 'false' }}"
       aria-controls="calculatorsSubmenu{{ $menuPrefix }}">
        {{ __('Calculators') }}
        <span class="sidebar-chevron">&rsaquo;</span>
    </a>
    <div class="collapse {{ $calculatorsOpen ? 'show' : '' }}" id="calculatorsSubmenu{{ $menuPrefix }}">
        <nav class="nav flex-column sidebar-submenu">
            <a class="nav-link {{ request()->routeIs('calculators.mortgage') ? 'active' : '' }}" href="{{ route('calculators.mortgage') }}" {!! $offcanvasDismiss !!}>{{ __('Mortgage') }}</a>
            <a class="nav-link {{ request()->routeIs('calculators.rental') ? 'active' : '' }}" href="{{ route('calculators.rental') }}" {!! $offcanvasDismiss !!}>{{ __('Rental NOI') }}</a>
            <a class="nav-link {{ request()->routeIs('calculators.commission') ? 'active' : '' }}" href="{{ route('calculators.commission') }}" {!! $offcanvasDismiss !!}>{{ __('Commission') }}</a>
        </nav>
    </div>

    @can('manage-users')
    <small class="text-secondary text-uppercase mt-3 mb-1">{{ __('Administration') }}</small>
    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" {!! $offcanvasDismiss !!}>{{ __('Users') }}</a>
    @endcan
</nav>
