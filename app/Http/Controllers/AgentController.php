<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(): View
    {
        return view('agents.index', ['agents' => Agent::orderBy('last_name')->paginate(20)]);
    }

    public function create(): View
    {
        return view('agents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Agent::create($request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:1'],
        ]));

        return redirect()->route('agents.index')->with('success', __('Agent created.'));
    }
}
