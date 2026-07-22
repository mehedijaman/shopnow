<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Order #{{ $order->id }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:#1e293b;">

@php
    $siteName = setting('branding.site_name', config('app.name'));
    $logoUrl  = setting('branding.logo_url');
@endphp

<!-- Outer wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f1f5f9;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            <!-- Card -->
            <table width="700" cellpadding="0" cellspacing="0" role="presentation" style="max-width:700px;width:100%;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);">

                <!-- ── HERO BAND ── -->
                <tr>
                    <td style="background:#1e40af;padding:20px 36px;">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td>
                                    <p style="margin:0;font-size:11px;color:#bfdbfe;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">New Order Received</p>
                                    <p style="margin:4px 0 0;font-size:22px;font-weight:700;color:#ffffff;">Order #{{ $order->id }}</p>
                                </td>
                                <td align="right" style="white-space:nowrap;">
                                    <p style="margin:0;font-size:12px;color:#bfdbfe;">{{ $order->created_at->format('d M Y') }}</p>
                                    <p style="margin:2px 0 0;font-size:12px;color:#bfdbfe;">{{ $order->created_at->format('h:i A') }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ── BODY ── -->
                <tr>
                    <td style="padding:32px 36px;">

                        <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.6;">
                            A new order has been placed on your store. Please review the details below and take the necessary action.
                        </p>

                        <!-- Customer Details -->
                        <p style="margin:0 0 10px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;border-bottom:1px solid #e2e8f0;padding-bottom:8px;">Customer Details</p>

                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:14px;">
                            <tr>
                                <td style="padding:6px 0;color:#64748b;width:140px;">Name</td>
                                <td style="padding:6px 0;color:#1e293b;font-weight:600;">{{ $order->name }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;color:#64748b;">Phone</td>
                                <td style="padding:6px 0;color:#1e293b;">{{ $order->phone }}</td>
                            </tr>
                            @if ($order->email)
                            <tr>
                                <td style="padding:6px 0;color:#64748b;">Email</td>
                                <td style="padding:6px 0;color:#1e293b;">{{ $order->email }}</td>
                            </tr>
                            @endif
                            @if ($order->district || $order->upazila)
                            <tr>
                                <td style="padding:6px 0;color:#64748b;vertical-align:top;">Area</td>
                                <td style="padding:6px 0;color:#1e293b;">
                                    {{ collect([$order->upazila, $order->district])->filter()->implode(', ') }}
                                </td>
                            </tr>
                            @endif
                            @if ($order->address)
                            <tr>
                                <td style="padding:6px 0;color:#64748b;vertical-align:top;">Address</td>
                                <td style="padding:6px 0;color:#1e293b;">{{ $order->address }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding:6px 0;color:#64748b;">Payment</td>
                                <td style="padding:6px 0;color:#1e293b;">
                                    {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ($order->payment_method ?? '—') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;color:#64748b;">Order Status</td>
                                <td style="padding:6px 0;">
                                    @php
                                        $statusStyles = [
                                            'pending'    => 'background:#fef3c7;color:#92400e;',
                                            'processing' => 'background:#dbeafe;color:#1e40af;',
                                            'shipped'    => 'background:#ede9fe;color:#5b21b6;',
                                            'delivered'  => 'background:#d1fae5;color:#065f46;',
                                            'completed'  => 'background:#dcfce7;color:#166534;',
                                            'cancelled'  => 'background:#fee2e2;color:#991b1b;',
                                        ];
                                        $style = $statusStyles[$order->status] ?? 'background:#f1f5f9;color:#475569;';
                                    @endphp
                                    <span style="display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;text-transform:capitalize;{{ $style }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <!-- Order Items -->
                        <p style="margin:28px 0 10px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;border-bottom:1px solid #e2e8f0;padding-bottom:8px;">Ordered Items</p>

                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:14px;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e2e8f0;">Product</th>
                                    <th style="padding:9px 10px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e2e8f0;">Qty</th>
                                    <th style="padding:9px 10px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e2e8f0;">Unit Price</th>
                                    <th style="padding:9px 10px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e2e8f0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderProducts as $item)
                                <tr>
                                    <td style="padding:10px 10px;border-bottom:1px solid #f1f5f9;color:#1e293b;">
                                        <strong style="font-size:14px;color:#1e293b;display:block;">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</strong>
                                        @if ($item->variation_label)
                                            <span style="font-size:12px;color:#2563eb;display:block;margin-top:2px;">Variation: {{ $item->variation_label }}</span>
                                        @elseif ($item->productVariation && $item->productVariation->name)
                                            <span style="font-size:12px;color:#2563eb;display:block;margin-top:2px;">Variation: {{ $item->productVariation->name }}</span>
                                        @endif
                                    </td>
                                    <td style="padding:10px 10px;border-bottom:1px solid #f1f5f9;text-align:center;color:#475569;">{{ $item->quantity }}</td>
                                    <td style="padding:10px 10px;border-bottom:1px solid #f1f5f9;text-align:right;color:#475569;">{{ number_format($item->unit_price, 2) }} Tk</td>
                                    <td style="padding:10px 10px;border-bottom:1px solid #f1f5f9;text-align:right;color:#1e293b;font-weight:700;">{{ number_format($item->total_price, 2) }} Tk</td>
                                </tr>

                                {{-- Bundle child items --}}
                                @if ($item->bundleItems && $item->bundleItems->count())
                                    @foreach ($item->bundleItems as $bi)
                                        <tr style="background:#f8fafc;">
                                            <td style="padding:6px 10px 6px 20px;font-size:12px;color:#64748b;border-bottom:1px solid #f1f5f9;">
                                                └ {{ $bi->name ?? 'Bundle Item' }}
                                            </td>
                                            <td style="padding:6px 10px;font-size:12px;color:#64748b;text-align:center;border-bottom:1px solid #f1f5f9;">{{ $bi->quantity }}</td>
                                            <td style="padding:6px 10px;font-size:12px;color:#64748b;text-align:right;border-bottom:1px solid #f1f5f9;">{{ number_format($bi->unit_price, 2) }} Tk</td>
                                            <td style="padding:6px 10px;font-size:12px;color:#64748b;text-align:right;border-bottom:1px solid #f1f5f9;">{{ number_format($bi->total_price, 2) }} Tk</td>
                                        </tr>
                                    @endforeach
                                @endif
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Totals -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:14px;margin-top:4px;">
                            <tr>
                                <td style="padding:6px 10px;color:#64748b;text-align:right;">Subtotal</td>
                                <td style="padding:6px 10px;text-align:right;width:120px;color:#1e293b;">{{ number_format($order->subtotal, 2) }} Tk</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 10px;color:#64748b;text-align:right;">Shipping</td>
                                <td style="padding:6px 10px;text-align:right;color:#1e293b;">{{ number_format($order->shipping, 2) }} Tk</td>
                            </tr>
                            @if ($order->tax > 0)
                            <tr>
                                <td style="padding:6px 10px;color:#64748b;text-align:right;">Tax</td>
                                <td style="padding:6px 10px;text-align:right;color:#1e293b;">{{ number_format($order->tax, 2) }} Tk</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding:10px 10px 6px;text-align:right;font-weight:700;font-size:15px;color:#0f172a;border-top:2px solid #e2e8f0;">Grand Total</td>
                                <td style="padding:10px 10px 6px;text-align:right;font-weight:700;font-size:15px;color:#1e40af;border-top:2px solid #e2e8f0;">{{ number_format($order->total, 2) }} Tk</td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- ── FOOTER ── -->
                <tr>
                    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 36px;text-align:center;">
                        <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                            This is an automated notification from <strong style="color:#64748b;">{{ $siteName }}</strong>.<br>
                            Please do not reply to this email.
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Card -->

        </td>
    </tr>
</table>

</body>
</html>
