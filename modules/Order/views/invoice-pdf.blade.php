<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #1a56db;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-info {
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .info-block {
            line-height: 1.6;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta table {
            float: right;
        }
        .invoice-meta td {
            padding: 2px 0;
            font-size: 13px;
        }
        .invoice-meta td.label {
            color: #666;
            padding-right: 15px;
            text-align: right;
        }
        .invoice-meta td.value {
            font-weight: bold;
            text-align: left;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 10px 12px;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .items-table tr.bundle-item td {
            background-color: #f9fafb;
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 12px;
            color: #666;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 6px 12px;
            font-size: 14px;
        }
        .summary-table tr.total-row td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #def7ec;
            color: #03543f;
        }
        .badge-pending {
            background-color: #fdf6b2;
            color: #723b11;
        }
        .badge-danger {
            background-color: #fde8e8;
            color: #9b1c1c;
        }
        .notes-section {
            margin-top: 40px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #e5e7eb;
            font-size: 13px;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo">{{ config('app.name', 'ShopNow') }}</div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">Your Trusted Shopping Partner</div>
                </td>
                <td class="company-info">
                    <strong>ShopNow Ltd.</strong><br>
                    House 45, Road 11, Banani<br>
                    Dhaka - 1213, Bangladesh<br>
                    Email: support@shopnow.com<br>
                    Phone: +880 1700-000000
                </td>
            </tr>
        </table>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td>
                    <div class="section-title">Billing & Shipping To</div>
                    <div class="info-block">
                        <strong>{{ $order->name }}</strong><br>
                        Phone: {{ $order->phone }}<br>
                        @if($order->email)
                            Email: {{ $order->email }}<br>
                        @endif
                        @if($order->address)
                            Address: {{ $order->address }}<br>
                        @endif
                        @if($order->union || $order->upazila || $order->district || $order->division)
                            Location: {{ implode(', ', array_filter([$order->union, $order->upazila, $order->district, $order->division])) }}
                        @endif
                    </div>
                </td>
                <td class="invoice-meta">
                    <div class="section-title">Invoice Details</div>
                    <table>
                        <tr>
                            <td class="label">Invoice No:</td>
                            <td class="value">#{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <td class="label">Date:</td>
                            <td class="value">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Payment Method:</td>
                            <td class="value">{{ strtoupper($order->payment_method ?? 'COD') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Payment Status:</td>
                            <td class="value">
                                <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-pending' }}">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Order Status:</td>
                            <td class="value">
                                <span class="badge {{ $order->status === 'completed' || $order->status === 'delivered' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-pending') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product Description</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 20%;">Unit Price</th>
                    <th class="text-right" style="width: 15%;">Discount</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderProducts as $item)
                    <tr>
                        <td>
                            <strong style="color: #111827;">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</strong>
                            @if($item->variation_label)
                                <div style="font-size: 12px; color: #4b5563; margin-top: 2px;">
                                    Option: {{ $item->variation_label }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }} Tk</td>
                        <td class="text-right">
                            @if($item->discount > 0)
                                <span style="color: #059669;">-{{ number_format($item->discount, 2) }} Tk</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right" style="font-weight: bold; color: #111827;">{{ number_format($item->total_price, 2) }} Tk</td>
                    </tr>
                    @if($item->bundleItems && $item->bundleItems->count() > 0)
                        @foreach($item->bundleItems as $bi)
                            <tr class="bundle-item">
                                <td style="padding-left: 30px;">
                                    — {{ $bi->name }} @if($bi->sku)<span style="color: #9ca3af;">({{ $bi->sku }})</span>@endif
                                </td>
                                <td class="text-center">{{ $bi->quantity }}</td>
                                <td class="text-right">{{ number_format($bi->unit_price, 2) }} Tk</td>
                                <td class="text-right">—</td>
                                <td class="text-right">{{ number_format($bi->total_price, 2) }} Tk</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- Totals & Summary -->
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if($order->notes)
                        <div class="notes-section">
                            <strong>Order Notes:</strong><br>
                            {{ $order->notes }}
                        </div>
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <table class="summary-table" style="width: 100%;">
                        <tr>
                            <td class="text-right" style="color: #6b7280;">Subtotal:</td>
                            <td class="text-right" style="width: 40%; font-weight: 500;">{{ number_format($order->subtotal, 2) }} Tk</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="color: #6b7280;">Shipping:</td>
                            <td class="text-right" style="font-weight: 500;">
                                @if($order->shipping == 0)
                                    <span style="color: #059669; font-weight: bold;">Free</span>
                                @else
                                    {{ number_format($order->shipping, 2) }} Tk
                                @endif
                            </td>
                        </tr>
                        @if($order->tax > 0)
                            <tr>
                                <td class="text-right" style="color: #6b7280;">Tax:</td>
                                <td class="text-right" style="font-weight: 500;">{{ number_format($order->tax, 2) }} Tk</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td class="text-right" style="color: #111827;">Total:</td>
                            <td class="text-right" style="color: #111827; font-weight: bold;">{{ number_format($order->total, 2) }} Tk</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="color: #6b7280; font-size: 12px;">Paid Amount:</td>
                            <td class="text-right" style="color: #059669; font-weight: 500; font-size: 12px;">{{ number_format($order->paid, 2) }} Tk</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="color: #6b7280; font-size: 12px;">Due Amount:</td>
                            <td class="text-right" style="color: {{ $order->due > 0 ? '#dc2626' : '#059669' }}; font-weight: 500; font-size: 12px;">
                                {{ number_format($order->due, 2) }} Tk
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            Thank you for shopping with {{ config('app.name', 'ShopNow') }}!<br>
            This is a computer-generated invoice and does not require a physical signature.
        </div>
    </div>
</body>
</html>
