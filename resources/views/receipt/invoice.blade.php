<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $invoice->number }}</title>
<style>
@php
    $w    = $width . 'mm';
    $fs   = $width === 58 ? '8.5px' : '10px';
    $fsSm = $width === 58 ? '7.5px' : '9px';
    $m    = $width === 58 ? '3mm 3mm' : '4mm 5mm';
@endphp

@page {
    size: {{ $w }} auto;
    margin: {{ $m }};
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    width: {{ $w }};
    max-width: {{ $w }};
    font-family: 'Courier New', Courier, monospace;
    font-size: {{ $fs }};
    color: #000;
    background: #fff;
    line-height: 1.4;
}

/* ── No-print toolbar ── */
.toolbar {
    display: flex;
    gap: 6px;
    justify-content: center;
    padding: 8px;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 10;
}
.toolbar button {
    padding: 5px 14px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #fff;
    font-size: 11px;
    font-family: system-ui, sans-serif;
    cursor: pointer;
    font-weight: 500;
    color: #334155;
}
.toolbar button.primary {
    background: #1e40af;
    color: #fff;
    border-color: #1e40af;
}
.toolbar button:hover { opacity: 0.85; }
.toolbar .size-note {
    font-size: 10px;
    font-family: system-ui, sans-serif;
    color: #64748b;
    display: flex;
    align-items: center;
}

@media print {
    .toolbar { display: none !important; }
    body { width: {{ $w }}; }
}

/* ── Receipt body ── */
.receipt {
    padding: {{ $width === 58 ? '4px 2px' : '6px 4px' }};
}

.center { text-align: center; }
.right  { text-align: right; }
.bold   { font-weight: 700; }

.company-name {
    font-size: {{ $width === 58 ? '11px' : '13px' }};
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.company-sub {
    font-size: {{ $fsSm }};
    text-align: center;
    color: #333;
}

.divider {
    border: none;
    border-top: 1px dashed #888;
    margin: 5px 0;
}
.divider-solid {
    border: none;
    border-top: 1px solid #000;
    margin: 5px 0;
}

.meta {
    display: flex;
    justify-content: space-between;
    font-size: {{ $fsSm }};
    margin: 2px 0;
}

/* Items */
.items-header {
    display: flex;
    justify-content: space-between;
    font-size: {{ $fsSm }};
    font-weight: 700;
    margin-bottom: 2px;
    padding-bottom: 2px;
    border-bottom: 1px solid #000;
}
.item-row {
    margin-bottom: 3px;
}
.item-name {
    font-size: {{ $fs }};
    word-break: break-word;
}
.item-detail {
    display: flex;
    justify-content: space-between;
    font-size: {{ $fsSm }};
    color: #333;
    padding-left: 6px;
}

/* Totals */
.total-row {
    display: flex;
    justify-content: space-between;
    font-size: {{ $fs }};
    margin: 1px 0;
}
.total-row.grand {
    font-weight: 700;
    font-size: {{ $width === 58 ? '12px' : '14px' }};
    margin-top: 3px;
    padding-top: 3px;
    border-top: 1px solid #000;
}
.total-row.balance {
    font-weight: 700;
    font-size: {{ $width === 58 ? '10px' : '12px' }};
}

/* Payments */
.pay-row {
    display: flex;
    justify-content: space-between;
    font-size: {{ $fsSm }};
    margin: 1px 0;
}

.footer-msg {
    text-align: center;
    font-size: {{ $fsSm }};
    margin-top: 6px;
}

.badge {
    font-size: {{ $fsSm }};
    border: 1px solid #000;
    padding: 0 3px;
    border-radius: 2px;
}
</style>
</head>
<body>

{{-- ── Toolbar (hidden on print) ── --}}
<div class="toolbar">
    <button class="primary" onclick="window.print()">🖨️ Imprimir</button>
    <a href="/invoices/{{ $invoice->id }}/receipt?width={{ $width === 80 ? 58 : 80 }}" style="text-decoration:none;">
        <button>Cambiar a {{ $width === 80 ? '58mm' : '80mm' }}</button>
    </a>
    <a href="/invoices/{{ $invoice->id }}" style="text-decoration:none;">
        <button>← Factura</button>
    </a>
    <span class="size-note">{{ $width }}mm</span>
</div>

{{-- ── Receipt ── --}}
<div class="receipt">

    {{-- Business header --}}
    <div class="company-name">{{ $settings['business_name'] }}</div>
    @if(!empty($settings['business_address']))
        <div class="company-sub">{{ $settings['business_address'] }}</div>
    @endif
    @if(!empty($settings['business_phone']))
        <div class="company-sub">Tel: {{ $settings['business_phone'] }}</div>
    @endif
    @if(!empty($settings['business_email']))
        <div class="company-sub">{{ $settings['business_email'] }}</div>
    @endif

    <hr class="divider-solid" style="margin-top:6px;">

    {{-- Invoice meta --}}
    <div class="meta"><span>Factura:</span><span class="bold">{{ $invoice->number }}</span></div>
    <div class="meta"><span>Fecha:</span><span>{{ $invoice->created_at->format('m/d/Y H:i') }}</span></div>
    @if($invoice->createdBy)
        <div class="meta"><span>Cajero:</span><span>{{ $invoice->createdBy->name }}</span></div>
    @endif
    @if($invoice->customer)
        <div class="meta"><span>Cliente:</span><span>{{ $invoice->customer->name }}</span></div>
        @if($invoice->customer->phone)
            <div class="meta"><span>Tel:</span><span>{{ $invoice->customer->phone }}</span></div>
        @endif
    @else
        <div class="meta"><span>Cliente:</span><span>Walk-in</span></div>
    @endif
    @if($invoice->workOrder)
        <div class="meta"><span>Orden:</span><span>{{ $invoice->workOrder->number }}</span></div>
        @if($invoice->workOrder->vehicle)
            <div class="meta">
                <span>Vehículo:</span>
                <span>{{ $invoice->workOrder->vehicle->year }} {{ $invoice->workOrder->vehicle->make }}</span>
            </div>
            @if($invoice->workOrder->vehicle->plate)
                <div class="meta"><span>Placa:</span><span>{{ $invoice->workOrder->vehicle->plate }}</span></div>
            @endif
        @endif
    @endif

    <hr class="divider">

    {{-- Items --}}
    <div class="items-header">
        <span>Descripción</span><span>Total</span>
    </div>

    @foreach($invoice->items as $item)
    <div class="item-row">
        <div class="item-name">{{ $item->name }}</div>
        <div class="item-detail">
            <span>{{ $item->qty }} × {{ $currency }}{{ number_format($item->unit_price, 2) }}
                @if($item->discount > 0) (−{{ $currency }}{{ number_format($item->discount, 2) }}) @endif
            </span>
            <span class="bold">{{ $currency }}{{ number_format($item->total, 2) }}</span>
        </div>
    </div>
    @endforeach

    <hr class="divider">

    {{-- Totals --}}
    <div class="total-row">
        <span>Subtotal</span>
        <span>{{ $currency }}{{ number_format($invoice->subtotal, 2) }}</span>
    </div>
    @if($invoice->discount > 0)
    <div class="total-row">
        <span>Descuento</span>
        <span>−{{ $currency }}{{ number_format($invoice->discount, 2) }}</span>
    </div>
    @endif
    @if($invoice->tax > 0)
    <div class="total-row">
        <span>Impuesto</span>
        <span>{{ $currency }}{{ number_format($invoice->tax, 2) }}</span>
    </div>
    @endif
    <div class="total-row grand">
        <span>TOTAL</span>
        <span>{{ $currency }}{{ number_format($invoice->total, 2) }}</span>
    </div>

    @if($invoice->amount_paid > 0)
    <hr class="divider">

    {{-- Payments --}}
    @foreach($invoice->payments as $pay)
    <div class="pay-row">
        <span>{{ ucfirst($pay->method) }}@if($pay->reference) #{{ $pay->reference }}@endif</span>
        <span>{{ $currency }}{{ number_format($pay->amount, 2) }}</span>
    </div>
    @endforeach

    <div class="total-row" style="margin-top:2px;">
        <span>Pagado</span>
        <span>{{ $currency }}{{ number_format($invoice->amount_paid, 2) }}</span>
    </div>

    @if($invoice->balance > 0)
    <div class="total-row balance">
        <span>SALDO</span>
        <span>{{ $currency }}{{ number_format($invoice->balance, 2) }}</span>
    </div>
    @else
    <div class="total-row" style="font-weight:700; color:#166534;">
        <span>CAMBIO</span>
        @php $cambio = $invoice->amount_paid - $invoice->total; @endphp
        <span>{{ $currency }}{{ number_format(max(0, $cambio), 2) }}</span>
    </div>
    @endif
    @endif

    @if($invoice->notes)
    <hr class="divider">
    <div style="font-size:{{ $fsSm }}; color:#333;">{{ $invoice->notes }}</div>
    @endif

    <hr class="divider-solid" style="margin-top:8px;">

    {{-- Footer --}}
    <div class="footer-msg">¡Gracias por su preferencia!</div>
    @if(!empty($settings['business_phone']))
        <div class="footer-msg" style="margin-top:2px;">{{ $settings['business_phone'] }}</div>
    @endif
    <div class="footer-msg" style="margin-top:4px; color:#555;">{{ $invoice->number }} · {{ $invoice->created_at->format('m/d/Y') }}</div>

</div>

<script>
// Auto-print only when opened directly (not navigated back to)
if (!document.referrer || performance.navigation?.type !== 2) {
    setTimeout(function () { window.print(); }, 400);
}
</script>
</body>
</html>
