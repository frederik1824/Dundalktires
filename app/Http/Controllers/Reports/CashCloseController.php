<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CashClose;
use App\Models\Payment;
use Illuminate\Http\Request;

class CashCloseController extends Controller
{
    public function open(Request $request)
    {
        $data = $request->validate([
            'date'         => 'required|date',
            'opening_fund' => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $branchId = auth()->user()->branch_id;

        if (CashClose::where('branch_id', $branchId)->where('date', $data['date'])->exists()) {
            return back()->withErrors(['date' => 'Ya existe un registro de caja para esta fecha.']);
        }

        CashClose::create([
            'branch_id'    => $branchId,
            'date'         => $data['date'],
            'opened_by'    => auth()->id(),
            'opened_at'    => now(),
            'opening_fund' => $data['opening_fund'],
            'status'       => 'open',
            'notes'        => $data['notes'] ?? null,
        ]);

        return back()->with('success', "Caja abierta para el {$data['date']}.");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'               => 'required|date',
            'physical_amounts'   => 'required|array',
            'physical_amounts.*' => 'numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $branchId = auth()->user()->branch_id;
        $date     = $data['date'];

        $payments = Payment::query()
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.branch_id', $branchId)
            ->whereDate('payments.paid_at', $date)
            ->select('payments.method', 'payments.amount')
            ->get();

        $systemAmounts = $payments->groupBy('method')
            ->map(fn ($g) => round($g->sum('amount'), 2))
            ->toArray();

        $systemTotal   = round($payments->sum('amount'), 2);
        $physAmounts   = array_map('floatval', $data['physical_amounts']);
        $physicalTotal = round(array_sum($physAmounts), 2);
        $difference    = round($physicalTotal - $systemTotal, 2);

        CashClose::updateOrCreate(
            ['branch_id' => $branchId, 'date' => $date],
            [
                'closed_by'        => auth()->id(),
                'closed_at'        => now(),
                'status'           => 'closed',
                'system_total'     => $systemTotal,
                'system_amounts'   => $systemAmounts,
                'physical_total'   => $physicalTotal,
                'physical_amounts' => $physAmounts,
                'difference'       => $difference,
                'notes'            => $data['notes'] ?? null,
            ]
        );

        return back()->with('success', "Cierre del {$date} confirmado.");
    }

    public function reopen(Request $request)
    {
        $data     = $request->validate(['date' => 'required|date']);
        $branchId = auth()->user()->branch_id;

        CashClose::where('branch_id', $branchId)
            ->where('date', $data['date'])
            ->update(['status' => 'open', 'closed_at' => null, 'closed_by' => null]);

        return back()->with('success', "Cierre del {$data['date']} reabierto.");
    }
}
