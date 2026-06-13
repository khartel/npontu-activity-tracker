<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $users = User::orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:users'],
            'email'       => ['required', 'email', 'unique:users'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'department'  => ['nullable', 'string', 'max:100'],
            'role'        => ['required', 'in:admin,personnel'],
            'password'    => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            ...$validated,
            'password'  => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:users,employee_id,' . $user->id],
            'email'       => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'       => ['nullable', 'string', 'max:20'],
            'department'  => ['nullable', 'string', 'max:100'],
            'role'        => ['required', 'in:admin,personnel'],
            'is_active'   => ['boolean'],
        ]);

        // Prevent admin from deactivating themselves
        if ($user->id === Auth::id() && !$request->boolean('is_active')) {
            return back()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        $user->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Optionally update password
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'User deactivated successfully.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403, 'Administrator access required.');
    }
}
