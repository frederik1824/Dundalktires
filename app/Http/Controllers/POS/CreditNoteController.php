<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class CreditNoteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage_sales'),
        ];
    }

    public function create(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'creditNotes']);

        $alreadyRefunded = $invoice->creditNotes->sum('amount');
        $maxRefund       = (float) $invoice->amount_paid - $alreadyRefunded;

        return Inertia::render('POS/CreditNote', [
            'invoice'         => $invoice,
            'maxRefund'       => max(0, $maxRefund),
            'alreadyRefunded' => $alreadyRefunded,
            'refundMethods'   => [
                'cash'         => 'Cash',
                'card'         => 'Card',
                'zelle'        => 'Zelle',
                'cashapp'      => 'Cash App',
                'transfer'     => 'Bank Transfer',
                'store_credit' => 'Store Credit',
                'other'        => 'Other',
            ],
        ]);
    }

    public function store(Request $request, Invoice $invoice)
    {
        $alreadyRefunded = $invoice->creditNotes()->sum('amount');
        $maxRefund       = (float) $invoice->amount_paid - $alreadyRefunded;

        $data = $request->validate([
            'reason'        => 'required|in:return,error,discount,other',
            'notes'         => 'nullable|string|max:500',
            'amount'        => "required|numeric|min:0.01|max:{$maxRefund}",
            'refund_method' => 'required|in:cash,card,zelle,cashapp,transfer,store_credit,other',
        ]);

        CreditNote::create([
            'invoice_id'    => $invoice->id,
            'branch_id'     => $invoice->branch_id,
            'created_by'    => auth()->id(),
            'number'        => CreditNote::nextNumber($invoice->branch_id),
            'reason'        => $data['reason'],
            'notes'         => $data['notes'] ?? null,
            'amount'        => $data['amount'],
            'refund_method' => $data['refund_method'],
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Credit note issued successfully.');
    }
}
