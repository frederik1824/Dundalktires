<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
    .page { padding: 36px; }

    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f3460; padding-bottom: 16px; margin-bottom: 24px; }
    .company-name { font-size: 18px; font-weight: 700; color: #0f3460; }
    .report-title { font-size: 16px; font-weight: 700; color: #0f3460; text-align: right; }
    .report-sub { font-size: 11px; color: #64748b; text-align: right; margin-top: 2px; }

    .kpi-row { display: flex; gap: 12px; margin-bottom: 24px; }
    .kpi { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
    .kpi-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; }
    .kpi-value { font-size: 18px; font-weight: 700; }
    .kpi-green { color: #16a34a; }
    .kpi-red   { color: #dc2626; }
    .kpi-blue  { color: #2563eb; }

    .section-title { font-size: 12px; font-weight: 700; color: #0f3460; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 10px; margin-top: 20px; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
    thead tr { background: #0f3460; color: #fff; }
    thead th { padding: 7px 8px; text-align: left; font-weight: 600; letter-spacing: 0.2px; }
    thead th.right { text-align: right; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
    tbody td.right { text-align: right; }
    tfoot tr { background: #f1f5f9; font-weight: 700; }
    tfoot td { padding: 7px 8px; border-top: 2px solid #e2e8f0; }
    tfoot td.right { text-align: right; }

    .footer { text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 24px; }
    .page-break { page-break-before: always; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div>
            <div class="company-name">{{ $settings['business_name'] }}</div>
        </div>
        <div>
            <div class="report-title">Financial Report</div>
            <div class="report-sub">{{ \Carbon\Carbon::parse($from)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</div>
            <div class="report-sub">Generated: {{ now()->format('M d, Y H:i') }}</div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="kpi-row">
        <div class="kpi">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-value kpi-green">{{ $settings['currency'] }}{{ number_format($salesTotal, 2) }}</div>
            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">{{ count($salesRows) }} invoices</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Total Expenses</div>
            <div class="kpi-value kpi-red">{{ $settings['currency'] }}{{ number_format($expenseTotal, 2) }}</div>
            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">{{ count($expenseRows) }} records</div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Net Profit</div>
            @php $profit = $salesTotal - $expenseTotal; @endphp
            <div class="kpi-value {{ $profit >= 0 ? 'kpi-green' : 'kpi-red' }}">
                {{ $settings['currency'] }}{{ number_format(abs($profit), 2) }}
                {{ $profit < 0 ? '(loss)' : '' }}
            </div>
            @if($salesTotal > 0)
            <div style="font-size:9px;color:#94a3b8;margin-top:2px;">
                Margin: {{ round($profit / $salesTotal * 100, 1) }}%
            </div>
            @endif
        </div>
    </div>

    <!-- Sales table -->
    @if(count($salesRows))
    <div class="section-title">Sales / Invoices</div>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Status</th>
                <th class="right">Subtotal</th>
                <th class="right">Tax</th>
                <th class="right">Discount</th>
                <th class="right">Total</th>
                <th class="right">Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesRows as $r)
            <tr>
                <td>{{ $r['invoice'] }}</td>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['customer'] }}</td>
                <td style="text-transform:capitalize;">{{ $r['status'] }}</td>
                <td class="right">{{ $settings['currency'] }}{{ $r['subtotal'] }}</td>
                <td class="right">{{ $r['tax'] > 0 ? $settings['currency'] . $r['tax'] : '—' }}</td>
                <td class="right">{{ $r['discount'] > 0 ? $settings['currency'] . $r['discount'] : '—' }}</td>
                <td class="right" style="font-weight:600;">{{ $settings['currency'] }}{{ $r['total'] }}</td>
                <td class="right">{{ $settings['currency'] }}{{ $r['paid'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="font-weight:700;">Total</td>
                <td class="right">{{ $settings['currency'] }}{{ number_format($salesTotal, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Expenses table -->
    @if(count($expenseRows))
    <div class="{{ count($salesRows) > 15 ? 'page-break' : '' }}">
    <div class="section-title">Expenses</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Vendor</th>
                <th>Method</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenseRows as $r)
            <tr>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['description'] }}</td>
                <td>{{ $r['category'] }}</td>
                <td>{{ $r['vendor'] ?: '—' }}</td>
                <td style="text-transform:capitalize;">{{ $r['method'] }}</td>
                <td class="right" style="font-weight:600;">{{ $settings['currency'] }}{{ $r['amount'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="font-weight:700;">Total</td>
                <td class="right">{{ $settings['currency'] }}{{ number_format($expenseTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    </div>
    @endif

    <div class="footer">
        {{ $settings['business_name'] }} · Report generated {{ now()->format('M d, Y \a\t H:i') }}
    </div>

</div>
</body>
</html>
