<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }
    .page { padding: 40px; max-width: 800px; margin: 0 auto; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; border-bottom: 2px solid #0f3460; padding-bottom: 20px; }
    .company-name { font-size: 22px; font-weight: 700; color: #0f3460; }
    .company-sub  { font-size: 11px; color: #64748b; margin-top: 2px; }
    .invoice-badge { text-align: right; }
    .invoice-badge .label { font-size: 22px; font-weight: 700; color: #0f3460; letter-spacing: 1px; }
    .invoice-badge .number { font-size: 14px; color: #475569; margin-top: 2px; }

    /* Status pill */
    .status { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-paid    { background: #dcfce7; color: #166534; }
    .status-partial { background: #fef3c7; color: #92400e; }
    .status-draft   { background: #f1f5f9; color: #475569; }
    .status-void    { background: #fee2e2; color: #991b1b; }

    /* Info grid */
    .info-grid { display: flex; gap: 20px; margin-bottom: 28px; }
    .info-box { flex: 1; }
    .info-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; }
    .info-value { font-size: 12px; color: #1e293b; font-weight: 500; }
    .info-value-sm { font-size: 11px; color: #475569; margin-top: 1px; }

    /* Items table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    thead tr { background: #0f3460; color: #fff; }
    thead th { padding: 9px 12px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.3px; }
    thead th.right { text-align: right; }
    tbody tr { border-bottom: 1px solid #e2e8f0; }
    tbody tr:last-child { border-bottom: none; }
    tbody td { padding: 8px 12px; font-size: 11px; }
    tbody td.right { text-align: right; }
    tbody tr:nth-child(even) { background: #f8fafc; }

    /* Totals */
    .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 28px; }
    .totals { width: 240px; }
    .totals-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
    .totals-row.border-top { border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 8px; }
    .totals-row.grand { font-weight: 700; font-size: 14px; color: #0f3460; }
    .totals-row.balance { font-weight: 700; color: #dc2626; }

    /* Payments */
    .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
    .payment-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 11px; border-bottom: 1px solid #f1f5f9; }

    /* Notes */
    .notes-box { background: #f8fafc; border-left: 3px solid #0f3460; padding: 10px 14px; font-size: 11px; color: #475569; margin-bottom: 20px; border-radius: 0 4px 4px 0; }

    /* Footer */
    .footer { text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 32px; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="company-name">{{ $settings['business_name'] ?? 'Dundalk Used & New Tires' }}</div>
            @if(!empty($settings['business_address']))
                <div class="company-sub">{{ $settings['business_address'] }}</div>
            @endif
            @if(!empty($settings['business_phone']))
                <div class="company-sub">Tel: {{ $settings['business_phone'] }}</div>
            @endif
            @if(!empty($settings['business_email']))
                <div class="company-sub">{{ $settings['business_email'] }}</div>
            @endif
        </div>
        <div class="invoice-badge">
            <div class="label">INVOICE</div>
            <div class="number">{{ $invoice->number }}</div>
            <div style="margin-top:6px;">
                <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>

    {{-- Bill To + Dates --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Bill To</div>
            @if($invoice->customer)
                <div class="info-value">{{ $invoice->customer->name }}</div>
                @if($invoice->customer->phone)
                    <div class="info-value-sm">{{ $invoice->customer->phone }}</div>
                @endif
                @if($invoice->customer->email)
                    <div class="info-value-sm">{{ $invoice->customer->email }}</div>
                @endif
            @else
                <div class="info-value">Walk-in Customer</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-label">Date</div>
            <div class="info-value">{{ $invoice->created_at->format('M d, Y') }}</div>
            @if($invoice->paid_at)
                <div class="info-label" style="margin-top:8px;">Paid On</div>
                <div class="info-value">{{ $invoice->paid_at->format('M d, Y') }}</div>
            @endif
        </div>
        @if($invoice->workOrder)
        <div class="info-box">
            <div class="info-label">Work Order</div>
            <div class="info-value">{{ $invoice->workOrder->number }}</div>
            @if($invoice->workOrder->vehicle)
                <div class="info-value-sm">
                    {{ $invoice->workOrder->vehicle->year }}
                    {{ $invoice->workOrder->vehicle->make }}
                    {{ $invoice->workOrder->vehicle->model }}
                </div>
                <div class="info-value-sm">{{ $invoice->workOrder->vehicle->plate }}</div>
            @endif
        </div>
        @endif
        <div class="info-box">
            <div class="info-label">Issued By</div>
            <div class="info-value">{{ $invoice->createdBy?->name ?? '—' }}</div>
        </div>
    </div>

    {{-- Line Items --}}
    <table>
        <thead>
            <tr>
                <th style="width:40%">Description</th>
                <th>Type</th>
                <th class="right">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Disc.</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td style="text-transform:capitalize;">{{ $item->type }}</td>
                <td class="right">{{ $item->qty }}</td>
                <td class="right">{{ $currency }}{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ $item->discount > 0 ? $currency . number_format($item->discount, 2) : '—' }}</td>
                <td class="right" style="font-weight:600;">{{ $currency }}{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrap">
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>{{ $currency }}{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->discount > 0)
            <div class="totals-row">
                <span>Discount</span>
                <span>−{{ $currency }}{{ number_format($invoice->discount, 2) }}</span>
            </div>
            @endif
            @if($invoice->tax > 0)
            <div class="totals-row">
                <span>Tax</span>
                <span>+{{ $currency }}{{ number_format($invoice->tax, 2) }}</span>
            </div>
            @endif
            <div class="totals-row border-top grand">
                <span>Total</span>
                <span>{{ $currency }}{{ number_format($invoice->total, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Paid</span>
                <span>{{ $currency }}{{ number_format($invoice->amount_paid, 2) }}</span>
            </div>
            @if($invoice->balance > 0)
            <div class="totals-row balance">
                <span>Balance Due</span>
                <span>{{ $currency }}{{ number_format($invoice->balance, 2) }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Payments --}}
    @if($invoice->payments->count())
    <div style="margin-bottom:24px;">
        <div class="section-title">Payment History</div>
        @foreach($invoice->payments as $pay)
        <div class="payment-row">
            <span>{{ $pay->paid_at->format('M d, Y') }} · {{ ucfirst($pay->method) }}
                @if($pay->reference) <span style="color:#94a3b8;">#{{ $pay->reference }}</span> @endif
            </span>
            <span style="font-weight:600;">{{ $currency }}{{ number_format($pay->amount, 2) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Notes --}}
    @if($invoice->notes)
    <div class="notes-box">
        <strong>Notes:</strong> {{ $invoice->notes }}
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        {{ $settings['business_name'] ?? 'Dundalk Used & New Tires' }}
        @if(!empty($settings['business_phone'])) · {{ $settings['business_phone'] }} @endif
        @if(!empty($settings['business_email'])) · {{ $settings['business_email'] }} @endif
        <br>Thank you for your business!
    </div>

</div>
</body>
</html>
