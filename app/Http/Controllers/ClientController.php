<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        return view('clients.index', ['clients' => Client::orderBy('last_name')->paginate(20)]);
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Client::create($request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:2'],
            'postal_code' => ['nullable', 'string'],
        ]));

        return redirect()->route('clients.index')->with('success', __('Client created.'));
    }
}
