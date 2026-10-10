<?php

namespace App\Http\Controllers\Appointments;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class AppointmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_work_orders|manage_work_orders', only: ['index', 'show']),
            new Middleware('permission:manage_work_orders', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Appointment::with(['customer', 'vehicle', 'assignedTo'])
            ->where('branch_id', auth()->user()->branch_id);

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('from')) {
            $query->whereDate('scheduled_at', '>=', $request->input('from'));
        }

        if ($request->input('to')) {
            $query->whereDate('scheduled_at', '<=', $request->input('to'));
        }

        if ($request->input('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('number', 'like', "%{$term}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$term}%"));
            });
        }

        $appointments = $query->orderBy('scheduled_at')->paginate(25)->withQueryString();

        $employees = User::where('branch_id', auth()->user()->branch_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'employees'    => $employees,
            'filters'      => $request->only('status', 'from', 'to', 'q'),
        ]);
    }

    public function create(Request $request)
    {
        $customers = Customer::where('branch_id', auth()->user()->branch_id)
            ->active()->orderBy('name')
            ->with('vehicles')
            ->get(['id', 'name', 'phone']);

        $employees = User::where('branch_id', auth()->user()->branch_id)
            ->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Appointments/Form', [
            'customers' => $customers,
            'employees' => $employees,
            'prefill'   => $request->only('customer_id', 'vehicle_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'      => 'nullable|exists:customers,id',
            'vehicle_id'       => 'nullable|exists:vehicles,id',
            'assigned_to'      => 'nullable|exists:users,id',
            'service_type'     => 'required|in:tire_change,rotation,alignment,inspection,repair,other',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $data['branch_id']  = auth()->user()->branch_id;
        $data['created_by'] = auth()->id();
        $data['number']     = Appointment::nextNumber();
        $data['status']     = 'scheduled';

        Appointment::create($data);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment scheduled.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['customer.vehicles', 'vehicle', 'assignedTo']);

        $customers = Customer::where('branch_id', auth()->user()->branch_id)
            ->active()->orderBy('name')
            ->with('vehicles')
            ->get(['id', 'name', 'phone']);

        $employees = User::where('branch_id', auth()->user()->branch_id)
            ->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Appointments/Form', [
            'appointment' => $appointment,
            'customers'   => $customers,
            'employees'   => $employees,
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'customer_id'      => 'nullable|exists:customers,id',
            'vehicle_id'       => 'nullable|exists:vehicles,id',
            'assigned_to'      => 'nullable|exists:users,id',
            'status'           => 'required|in:scheduled,confirmed,in_progress,completed,cancelled',
            'service_type'     => 'required|in:tire_change,rotation,alignment,inspection,repair,other',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $appointment->update($data);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('success', 'Appointment removed.');
    }
}
