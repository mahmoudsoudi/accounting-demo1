<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-users');

        return view('users.index', [
            'users' => User::with('role')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-users');

        return view('users.create', ['roles' => \App\Models\Role::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', __('User created successfully.'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('manage-users');

        return view('users.edit', [
            'user' => $user,
            'roles' => \App\Models\Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:4'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        if ($user->isAdmin() && ! $data['is_active']) {
            $activeAdmins = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeAdmins < 1) {
                return back()->withErrors(['is_active' => __('At least one admin must remain active.')]);
            }
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', __('User updated successfully.'));
    }
}
