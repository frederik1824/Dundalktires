<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_employees|manage_employees', only: ['index']),
            new Middleware('permission:manage_employees', only: ['create', 'store', 'edit', 'update', 'destroy', 'resetPassword']),
        ];
    }

    public function index(Request $request)
    {
        $employees = User::query()
            ->with('roles')
            ->when($request->input('q'), fn ($q, $term) =>
                $q->where(function ($q2) use ($term) {
                    $q2->where('name', 'like', "%{$term}%")
                       ->orWhere('email', 'like', "%{$term}%");
                })
            )
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'filters'   => $request->only('q'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Employees/Form', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'nullable|string|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'branch_id' => auth()->user()->branch_id,
        ]);

        if (! empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee created.');
    }

    public function edit(User $employee)
    {
        $employee->load('roles');

        return Inertia::render('Employees/Form', [
            'employee' => array_merge($employee->toArray(), [
                'role' => $employee->roles->first()?->name,
            ]),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function update(Request $request, User $employee)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => "required|email|unique:users,email,{$employee->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'nullable|string|exists:roles,name',
            'phone'    => 'nullable|string|max:20',
        ]);

        $employee->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            ...(! empty($data['password']) ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $employee->syncRoles(! empty($data['role']) ? [$data['role']] : []);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated.');
    }

    public function destroy(User $employee)
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $employee->delete();
        return redirect()->route('employees.index')
            ->with('success', 'Employee removed.');
    }

    public function resetPassword(Request $request, User $employee)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', "Password reset for {$employee->name}.");
    }
}
