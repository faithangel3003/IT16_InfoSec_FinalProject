<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $employees = Employee::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            })
            ->paginate($perPage)->withQueryString();

        // KPI Statistics
        $totalEmployees = Employee::count();
        $activeEmployees = User::whereIn('role', ['inventory_manager', 'room_manager', 'security'])->where('status', 'active')->count();
        $inactiveEmployees = User::whereIn('role', ['inventory_manager', 'room_manager', 'security'])->where('status', 'inactive')->count();
        $inventoryManagers = User::where('role', 'inventory_manager')->count();
        $roomManagers = User::where('role', 'room_manager')->count();

        return view('employees.index', compact('employees', 'perPage', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'inventoryManagers', 'roomManagers'));
    }

    public function edit(Employee $employee)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\\s]+$/',
            'address' => 'required|string|max:255',
            'contact_number' => ['required', 'string', 'max:15', 'regex:/^[0-9+\\-\\s()]+$/'],
            'sss_number' => ['required', 'string', 'max:20', 'regex:/^[0-9\\-]+$/'],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'role' => 'required|in:inventory_manager,room_manager,security',
            'status' => 'required|in:active,inactive',
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:12', 'confirmed', 'regex:/^[a-zA-Z0-9]+$/'];
        }

        $validated = $request->validate($rules, [
            'password.min' => 'Password must be at least 12 characters.',
            'password.regex' => 'Password must contain only alphanumeric characters (letters and numbers).',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'contact_number.regex' => 'Contact number can only contain numbers, +, -, spaces, and parentheses.',
            'sss_number.regex' => 'SSS number can only contain numbers and dashes.',
            'profile_picture.max' => 'Profile picture must not exceed 4MB.',
        ]);

        // Update the associated user
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($validated['password']);
        }
        
        $employee->user->update($userData);

        // Handle profile picture upload
        $profilePicture = $employee->profile_picture;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Update the employee details
        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'sss_number' => $validated['sss_number'],
            'profile_picture' => $profilePicture,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Delete the associated user and employee
        $employee->user->delete();
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:12', 'confirmed', 'regex:/^[a-zA-Z0-9]+$/'],
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required|string|max:255',
            'contact_number' => ['required', 'string', 'max:15', 'regex:/^[0-9+\-\s()]+$/'],
            'sss_number' => ['required', 'string', 'max:20', 'regex:/^[0-9\-]+$/'],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'role' => 'required|in:inventory_manager,room_manager,security',
            'status' => 'required|in:active,inactive',
        ], [
            'password.min' => 'Password must be at least 12 characters.',
            'password.regex' => 'Password must contain only alphanumeric characters (letters and numbers).',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'contact_number.regex' => 'Contact number can only contain numbers, +, -, spaces, and parentheses.',
            'sss_number.regex' => 'SSS number can only contain numbers and dashes.',
            'profile_picture.max' => 'Profile picture must not exceed 4MB.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        $profilePicture = 'images/TCEmployeeProfile.png';
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        Employee::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'sss_number' => $request->sss_number,
            'profile_picture' => $profilePicture,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee account created successfully!');
    }
}