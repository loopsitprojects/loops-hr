<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $departments = Department::all();
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:' . implode(',', [User::ROLE_SUPER_ADMIN, User::ROLE_HR_ADMIN, User::ROLE_MANAGER, User::ROLE_HOD])],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_super_admin' => $request->role === User::ROLE_SUPER_ADMIN,
            'department_id' => $request->department_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        Log::info('Deletion attempt for user ID: ' . $user->id . ' by admin ID: ' . auth()->id());

        if ($user->id === auth()->id()) {
            Log::warning('Self-deletion attempt blocked for admin ID: ' . auth()->id());
            
            if (request()->wantsJson()) {
                return response()->json(['error' => 'You cannot delete your own account.'], 403);
            }
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
            Log::info('User ID: ' . $user->id . ' successfully deleted.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user ID: ' . $user->id . '. Error: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Failed to delete user: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        }
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
