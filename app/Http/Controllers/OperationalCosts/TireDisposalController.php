<?php

namespace App\Http\Controllers\OperationalCosts;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TireDisposal;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TireDisposalController extends Controller
{
    public function store(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'qty_total' => 'required|integer|min:1',
            'qty_client_kept' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($data['qty_client_kept'] > $data['qty_total']) {
            return back()->withErrors(['qty_client_kept' => 'No puede ser mayor que el total.']);
        }

        $qtyForDisposal = $data['qty_total'] - $data['qty_client_kept'];

        if ($qtyForDisposal === 0) {
            return back()->with('success', 'Todas las llantas fueron entregadas al cliente.');
        }

        DB::transaction(function () use ($data, $workOrder, $qtyForDisposal) {
            $branchId = $workOrder->branch_id;
            $costPerUnit = (float) (Setting::get('tire_disposal_cost', null, $branchId) ?? 0);

            TireDisposal::create([
                'branch_id' => $branchId,
                'work_order_id' => $workOrder->id,
                'created_by' => auth()->id(),
                'qty_total' => $data['qty_total'],
                'qty_client_kept' => $data['qty_client_kept'],
                'qty_for_disposal' => $qtyForDisposal,
                'cost_per_unit' => $costPerUnit,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $workOrder->load('services', 'products', 'consumables', 'tireDisposals');
            $workOrder->refreshCosts();
        });

        return back()->with('success', 'Disposición de llantas registrada.');
    }

    public function destroy(WorkOrder $workOrder, TireDisposal $disposal)
    {
        $disposal->delete();

        $workOrder->load('services', 'products', 'consumables', 'tireDisposals');
        $workOrder->refreshCosts();

        return back()->with('success', 'Registro de disposición eliminado.');
    }
}
