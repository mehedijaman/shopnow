<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            font-size: 13px;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .invoice-container {
            padding: 40px;
            position: relative;
        }
        /* Top Accent Bar */
        .top-bar {
            height: 8px;
            background: linear-gradient(90deg, #0f172a 0%, #2563eb 100%);
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 35px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .logo span {
            color: #2563eb;
        }
        .tagline {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }
        .company-info {
            text-align: right;
            font-size: 11px;
            color: #475569;
            line-height: 1.6;
        }
        .invoice-title-container {
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }
        .details-table td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 10px;
            letter-spacing: 0.8px;
        }
        .info-block {
            line-height: 1.6;
            color: #334155;
        }
        .info-block strong {
            color: #0f172a;
            font-size: 14px;
        }
        .invoice-meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-meta-table td {
            padding: 4px 0;
            font-size: 12px;
            width: auto;
        }
        .invoice-meta-table td.label {
            color: #64748b;
            font-weight: 600;
            text-align: right;
            padding-right: 15px;
        }
        .invoice-meta-table td.value {
            font-weight: 700;
            color: #0f172a;
            text-align: left;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 12px 14px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #334155;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .items-table tr.bundle-item td {
            background-color: #f8fafc;
            padding-top: 8px;
            padding-bottom: 8px;
            font-size: 12px;
            color: #64748b;
            border-bottom: 1px dashed #e2e8f0;
        }
        .items-table tr.bundle-item td.first-col {
            padding-left: 30px;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .summary-wrapper {
            width: 100%;
            margin-top: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 12px;
            font-size: 13px;
            color: #475569;
        }
        .summary-table td.label {
            text-align: right;
            font-weight: 600;
        }
        .summary-table td.value {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
            width: 35%;
        }
        .summary-table tr.total-row td {
            border-top: 2px solid #0f172a;
            padding-top: 12px;
            padding-bottom: 12px;
        }
        .summary-table tr.total-row td.label {
            font-size: 15px;
            color: #0f172a;
            font-weight: 800;
        }
        .summary-table tr.total-row td.value {
            font-size: 17px;
            color: #2563eb;
            font-weight: 800;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-pending {
            background-color: #fef9c3;
            color: #a16207;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .notes-section {
            padding: 15px 20px;
            background-color: #f8fafc;
            border-left: 4px solid #cbd5e1;
            border-radius: 0 8px 8px 0;
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
        }
        .notes-section strong {
            color: #0f172a;
            display: block;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        /* Stamp / Status Overlay watermark style */
        .stamp-container {
            position: relative;
        }
        .stamp {
            position: absolute;
            top: 20px;
            left: 50px;
            border: 3px double;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 8px 20px;
            border-radius: 8px;
            opacity: 0.8;
            transform: rotate(-12deg);
        }
        .stamp-paid {
            border-color: #16a34a;
            color: #16a34a;
            background-color: #f0fdf4;
        }
        .stamp-unpaid {
            border-color: #dc2626;
            color: #dc2626;
            background-color: #fef2f2;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 25px;
            line-height: 1.6;
        }
        .footer strong {
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <div class="invoice-container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <div class="logo">SHOP<span>NOW</span></div>
                    <div class="tagline">Your Premium Retail Store Partner</div>
                </td>
                <td class="company-info">
                    <strong>ShopNow Ltd.</strong><br>
                    Level 12, Crystal Plaza, Banani Road 11<br>
                    Dhaka - 1213, Bangladesh<br>
                    <strong>Email:</strong> billing@shopnow.com &nbsp;|&nbsp; <strong>Support:</strong> +880 1700-000000
                </td>
            </tr>
        </table>

        <!-- Invoice Title banner -->
        <div class="invoice-title-container">
            <table style="width: 100%;">
                <tr>
                    <td class="invoice-title">Retail Invoice</td>
                    <td style="text-align: right; font-size: 14px; font-weight: 700; color: #475569;">
                        Invoice ID: <span style="color: #0f172a;">#{{ $order->id }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Details grid -->
        <table class="details-table">
            <tr>
                <td>
                    <div class="section-title">Billing & Shipping Details</div>
                    <div class="info-block">
                        <strong>{{ $order->name }}</strong><br>
                        <span style="color: #64748b; font-weight: 600;">Phone:</span> {{ $order->phone }}<br>
                        @if($order->email)
                            <span style="color: #64748b; font-weight: 600;">Email:</span> {{ $order->email }}<br>
                        @endif
                        @if($order->address)
                            <span style="color: #64748b; font-weight: 600;">Address:</span> {{ $order->address }}<br>
                        @endif
                        @if($order->union || $order->upazila || $order->district || $order->division)
                            <span style="color: #64748b; font-weight: 600;">Location:</span> {{ implode(', ', array_filter([$order->union, $order->upazila, $order->district, $order->division])) }}
                        @endif
                    </div>
                </td>
                <td style="padding-left: 40px;">
                    <div class="section-title">Payment & Shipping Info</div>
                    <table class="invoice-meta-table">
                        <tr>
                            <td class="label">Invoice Date:</td>
                            <td class="value">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Method:</td>
                            <td class="value">
                                @if(strtolower($order->payment_method) === 'cod')
                                    Cash On Delivery (COD)
                                @else
                                    {{ strtoupper($order->payment_method ?? 'COD') }}
                                @endif
                            </td>
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
                            <td class="label">Delivery Status:</td>
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
                    <th class="text-right" style="width: 18%;">Discount</th>
                    <th class="text-right" style="width: 22%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderProducts as $item)
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">
                            {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                            @if($item->variation_label)
                                <div style="font-size: 11px; color: #2563eb; margin-top: 3px; font-weight: 500;">
                                    Option: {{ $item->variation_label }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center" style="font-weight: 700;">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }} Tk</td>
                        <td class="text-right">
                            @if($item->discount > 0)
                                <span style="color: #16a34a; font-weight: 600;">-{{ number_format($item->discount, 2) }} Tk</span>
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </td>
                        <td class="text-right" style="font-weight: 700; color: #0f172a;">{{ number_format($item->total_price, 2) }} Tk</td>
                    </tr>
                    @if($item->bundleItems && $item->bundleItems->count() > 0)
                        @foreach($item->bundleItems as $bi)
                            <tr class="bundle-item">
                                <td class="first-col">
                                    <span style="color: #94a3b8; font-weight: bold;">└─</span> {{ $bi->name }} 
                                    @if($bi->sku)<span style="color: #94a3b8; font-size: 10px;">({{ $bi->sku }})</span>@endif
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

        <!-- Totals & Notes Section -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 55%; vertical-align: top; padding-right: 30px;">
                    @if($order->notes)
                        <div class="notes-section">
                            <strong>Customer Instructions</strong>
                            {{ $order->notes }}
                        </div>
                    @endif
                    
                    <div class="stamp-container">
                        @if($order->payment_status === 'paid')
                            <div class="stamp stamp-paid">PAID IN FULL</div>
                        @else
                            <div class="stamp stamp-unpaid">AMOUNT DUE</div>
                        @endif
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <div class="summary-wrapper">
                        <table class="summary-table">
                            <tr>
                                <td class="label">Subtotal:</td>
                                <td class="value">{{ number_format($order->subtotal, 2) }} Tk</td>
                            </tr>
                            <tr>
                                <td class="label">Shipping:</td>
                                <td class="value">
                                    @if($order->shipping == 0)
                                        <span style="color: #16a34a;">Free</span>
                                    @else
                                        {{ number_format($order->shipping, 2) }} Tk
                                    @endif
                                </td>
                            </tr>
                            @if($order->tax > 0)
                                <tr>
                                    <td class="label">Tax:</td>
                                    <td class="value">{{ number_format($order->tax, 2) }} Tk</td>
                                </tr>
                            @endif
                            <tr class="total-row">
                                <td class="label">Grand Total:</td>
                                <td class="value">{{ number_format($order->total, 2) }} Tk</td>
                            </tr>
                            <tr>
                                <td class="label" style="font-size: 11px; color: #64748b;">Paid Amount:</td>
                                <td class="value" style="font-size: 12px; color: #16a34a;">{{ number_format($order->paid, 2) }} Tk</td>
                            </tr>
                            <tr>
                                <td class="label" style="font-size: 11px; color: #64748b;">Due Amount:</td>
                                <td class="value" style="font-size: 12px; color: {{ $order->due > 0 ? '#dc2626' : '#16a34a' }};">
                                    {{ number_format($order->due, 2) }} Tk
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            Thank you for shopping with <strong>{{ config('app.name', 'ShopNow') }}</strong>!<br>
            If you have any questions about this invoice, please contact our support team.<br>
            <span style="font-size: 10px; color: #cbd5e1; margin-top: 10px; display: block;">This is a system-generated document. No physical signature is required.</span>
        </div>
    </div>
</body>
</html>
