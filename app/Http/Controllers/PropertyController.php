<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        return view('properties.index', ['properties' => Property::with('client')->orderBy('address')->paginate(20)]);
    }

    public function create(): View
    {
        return view('properties.create', ['clients' => Client::where('is_active', true)->orderBy('last_name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Property::create($request->validate([
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'province' => ['required', 'string', 'max:2'],
            'postal_code' => ['nullable', 'string'],
            'property_type' => ['nullable', 'string'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'purchase_price' => ['nullable', 'numeric'],
            'current_value' => ['nullable', 'numeric'],
        ]));

        return redirect()->route('properties.index')->with('success', __('Property created.'));
    }
}
