<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExpenseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_expenses|manage_expenses', only: ['index']),
            new Middleware('permission:manage_expenses', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $expenses = Expense::query()
            ->with('category', 'createdBy')
            ->search($request->input('q'))
            ->when($request->input('category'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->input('type'), fn ($q, $v) =>
                $q->whereHas('category', fn ($c) => $c->where('type', $v))
            )
            ->when($request->input('from'), fn ($q, $v) => $q->where('paid_at', '>=', $v))
            ->when($request->input('to'),   fn ($q, $v) => $q->where('paid_at', '<=', $v))
            ->orderByDesc('paid_at')
            ->paginate(25)->withQueryString();

        $totals = Expense::query()
            ->when($request->input('from'), fn ($q, $v) => $q->where('paid_at', '>=', $v))
            ->when($request->input('to'),   fn ($q, $v) => $q->where('paid_at', '<=', $v))
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        return Inertia::render('Expenses/Index', [
            'expenses'   => $expenses,
            'categories' => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
            'filters'    => $request->only('q', 'category', 'type', 'from', 'to'),
            'totals'     => $totals,
        ]);
    }

    public function create()
    {
        return Inertia::render('Expenses/Form', [
            'categories'     => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
            'paymentMethods' => [
                'cash' => 'Cash', 'card' => 'Card', 'zelle' => 'Zelle',
                'cashapp' => 'Cash App', 'transfer' => 'Bank Transfer',
                'check' => 'Check', 'other' => 'Other',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'    => 'nullable|exists:expense_categories,id',
            'description'    => 'required|string|max:200',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,zelle,cashapp,transfer,check,other',
            'vendor'         => 'nullable|string|max:100',
            'reference'      => 'nullable|string|max:100',
            'is_recurring'   => 'boolean',
            'recurrence'     => 'nullable|in:weekly,biweekly,monthly,yearly',
            'paid_at'        => 'required|date',
            'notes'          => 'nullable|string|max:1000',
            'evidence'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('evidence')) {
            $data['evidence'] = $request->file('evidence')
                ->store('expenses', 'public');
        }

        $data['branch_id']  = auth()->user()->branch_id;
        $data['created_by'] = auth()->id();

        Expense::create($data);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded.');
    }

    public function edit(Expense $expense)
    {
        return Inertia::render('Expenses/Form', [
            'expense'        => $expense,
            'categories'     => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
            'paymentMethods' => [
                'cash' => 'Cash', 'card' => 'Card', 'zelle' => 'Zelle',
                'cashapp' => 'Cash App', 'transfer' => 'Bank Transfer',
                'check' => 'Check', 'other' => 'Other',
            ],
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'category_id'    => 'nullable|exists:expense_categories,id',
            'description'    => 'required|string|max:200',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,zelle,cashapp,transfer,check,other',
            'vendor'         => 'nullable|string|max:100',
            'reference'      => 'nullable|string|max:100',
            'is_recurring'   => 'boolean',
            'recurrence'     => 'nullable|in:weekly,biweekly,monthly,yearly',
            'paid_at'        => 'required|date',
            'notes'          => 'nullable|string|max:1000',
            'evidence'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('evidence')) {
            if ($expense->evidence) Storage::disk('public')->delete($expense->evidence);
            $data['evidence'] = $request->file('evidence')
                ->store('expenses', 'public');
        }

        $expense->update($data);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->evidence) Storage::disk('public')->delete($expense->evidence);
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense removed.');
    }
}
