<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmation #{{ $order->id }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:#1e293b;-webkit-font-smoothing:antialiased;">

@php
    $siteName = setting('branding.site_name', config('app.name'));
    $logoUrl = setting('branding.logo_url');
    $supportEmail = setting('general.admin_email');
    $phones = setting('contact.phone');
    $supportPhone = is_array($phones) && count($phones) > 0 ? $phones[0] : null;
@endphp

<!-- Outer wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f8fafc;padding:32px 16px;">
    <tr>
        <td align="center">

            <!-- Container Card -->
            <table width="640" cellpadding="0" cellspacing="0" role="presentation" style="max-width:640px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #e2e8f0;">

                <!-- ── HEADER / BRANDING ── -->
                <tr>
                    <td style="background:#2563eb;padding:24px 32px;text-align:center;">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-height:44px;width:auto;display:block;margin:0 auto;" />
                        @else
                            <h1 style="margin:0;font-size:24px;font-weight:800;color:#ffffff;letter-spacing:-0.02em;">{{ $siteName }}</h1>
                        @endif
                    </td>
                </tr>

                <!-- ── HERO / THANK YOU BANNER ── -->
                <tr>
                    <td style="padding:32px 32px 16px;text-align:center;background:#eff6ff;border-bottom:1px solid #dbeafe;">
                        <div style="display:inline-block;width:48px;height:48px;line-height:48px;background:#22c55e;color:#ffffff;border-radius:50%;font-size:24px;margin-bottom:12px;">✓</div>
                        <h2 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#1e3a8a;">Thank you for your order!</h2>
                        <p style="margin:0;font-size:14px;color:#3b82f6;">Hi <strong>{{ $order->name }}</strong>, we've received your order and are getting it ready.</p>
                    </td>
                </tr>

                <!-- ── BODY CONTENT ── -->
                <tr>
                    <td style="padding:28px 32px;">

                        <!-- Order & Delivery Info Cards Grid -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
                            <tr>
                                <td width="48%" style="vertical-align:top;background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;">
                                    <p style="margin:0 0 8px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Order Details</p>
                                    <p style="margin:0 0 4px;font-size:13px;color:#1e293b;"><strong>Order Number:</strong> #{{ $order->id }}</p>
                                    <p style="margin:0 0 4px;font-size:13px;color:#1e293b;"><strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                                    <p style="margin:0 0 4px;font-size:13px;color:#1e293b;"><strong>Payment:</strong> {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ($order->payment_method ? ucfirst($order->payment_method) : '—') }}</p>
                                    <p style="margin:0;font-size:13px;color:#1e293b;">
                                        <strong>Status:</strong>
                                        <span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">
                                            {{ ucfirst($order->payment_status ?? 'Pending') }}
                                        </span>
                                    </p>
                                </td>
                                <td width="4%"></td>
                                <td width="48%" style="vertical-align:top;background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;">
                                    <p style="margin:0 0 8px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Delivery Information</p>
                                    <p style="margin:0 0 4px;font-size:13px;color:#1e293b;"><strong>Recipient:</strong> {{ $order->name }}</p>
                                    <p style="margin:0 0 4px;font-size:13px;color:#1e293b;"><strong>Phone:</strong> {{ $order->phone }}</p>
                                    @if ($order->district || $order->upazila)
                                        <p style="margin:0 0 4px;font-size:13px;color:#1e293b;"><strong>Area:</strong> {{ collect([$order->upazila, $order->district])->filter()->implode(', ') }}</p>
                                    @endif
                                    @if ($order->address)
                                        <p style="margin:0;font-size:13px;color:#1e293b;"><strong>Address:</strong> {{ $order->address }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- Items Table Header -->
                        <h3 style="margin:0 0 12px;font-size:14px;font-weight:700;color:#0f172a;text-transform:uppercase;letter-spacing:0.04em;">Order Items</h3>

                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:13px;border-collapse:collapse;margin-bottom:20px;">
                            <thead>
                                <tr style="background:#f1f5f9;">
                                    <th style="padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #cbd5e1;">Item Description</th>
                                    <th style="padding:10px 12px;text-align:center;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #cbd5e1;width:60px;">Qty</th>
                                    <th style="padding:10px 12px;text-align:right;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #cbd5e1;width:100px;">Price</th>
                                    <th style="padding:10px 12px;text-align:right;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #cbd5e1;width:100px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderProducts as $item)
                                    <tr>
                                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;color:#0f172a;">
                                            <strong style="font-size:14px;">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</strong>
                                            @if ($item->variation_label)
                                                <p style="margin:2px 0 0;font-size:12px;color:#2563eb;">{{ $item->variation_label }}</p>
                                            @endif
                                        </td>
                                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;text-align:center;color:#475569;font-weight:600;">{{ $item->quantity }}</td>
                                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;text-align:right;color:#475569;">{{ number_format($item->unit_price, 2) }} Tk</td>
                                        <td style="padding:12px;border-bottom:1px solid #f1f5f9;text-align:right;color:#0f172a;font-weight:700;">{{ number_format($item->total_price, 2) }} Tk</td>
                                    </tr>

                                    {{-- Bundle child items --}}
                                    @if ($item->bundleItems && $item->bundleItems->count())
                                        @foreach ($item->bundleItems as $bi)
                                            <tr style="background:#f8fafc;">
                                                <td style="padding:6px 12px 6px 24px;font-size:12px;color:#64748b;border-bottom:1px solid #f1f5f9;">
                                                    └ {{ $bi->name ?? 'Bundle Item' }}
                                                </td>
                                                <td style="padding:6px 12px;font-size:12px;color:#64748b;text-align:center;border-bottom:1px solid #f1f5f9;">{{ $bi->quantity }}</td>
                                                <td style="padding:6px 12px;font-size:12px;color:#64748b;text-align:right;border-bottom:1px solid #f1f5f9;">{{ number_format($bi->unit_price, 2) }} Tk</td>
                                                <td style="padding:6px 12px;font-size:12px;color:#64748b;text-align:right;border-bottom:1px solid #f1f5f9;">{{ number_format($bi->total_price, 2) }} Tk</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Totals Breakdown -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:13px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:4px 12px;color:#64748b;text-align:right;">Subtotal</td>
                                <td style="padding:4px 12px;text-align:right;width:120px;color:#1e293b;font-weight:600;">{{ number_format($order->subtotal, 2) }} Tk</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 12px;color:#64748b;text-align:right;">Shipping Charge</td>
                                <td style="padding:4px 12px;text-align:right;color:#1e293b;font-weight:600;">
                                    @if ($order->shipping == 0)
                                        <span style="color:#16a34a;font-weight:700;">Free</span>
                                    @else
                                        {{ number_format($order->shipping, 2) }} Tk
                                    @endif
                                </td>
                            </tr>
                            @if ($order->tax > 0)
                                <tr>
                                    <td style="padding:4px 12px;color:#64748b;text-align:right;">Tax</td>
                                    <td style="padding:4px 12px;text-align:right;color:#1e293b;font-weight:600;">{{ number_format($order->tax, 2) }} Tk</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="padding:10px 12px 4px;text-align:right;font-weight:700;font-size:15px;color:#0f172a;border-top:2px solid #e2e8f0;">Total Amount</td>
                                <td style="padding:10px 12px 4px;text-align:right;font-weight:800;font-size:16px;color:#2563eb;border-top:2px solid #e2e8f0;">{{ number_format($order->total, 2) }} Tk</td>
                            </tr>
                        </table>

                        <!-- Action Button -->
                        <div style="text-align:center;margin:32px 0 16px;">
                            <a href="{{ url('/order-confirm/'.$order->id) }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 28px;border-radius:8px;box-shadow:0 2px 8px rgba(37,99,235,0.25);">
                                View Order Confirmation
                            </a>
                        </div>

                    </td>
                </tr>

                <!-- ── FOOTER ── -->
                <tr>
                    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:24px 32px;text-align:center;">
                        <p style="margin:0 0 8px;font-size:13px;color:#475569;">Need help with your order?</p>
                        <p style="margin:0 0 16px;font-size:12px;color:#64748b;">
                            @if ($supportEmail)
                                Email us at <a href="mailto:{{ $supportEmail }}" style="color:#2563eb;text-decoration:none;">{{ $supportEmail }}</a>
                            @endif
                            @if ($supportPhone)
                                &nbsp;•&nbsp; Call us at <strong style="color:#1e293b;">{{ $supportPhone }}</strong>
                            @endif
                        </p>
                        <p style="margin:0;font-size:11px;color:#94a3b8;">
                            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Container Card -->

        </td>
    </tr>
</table>

</body>
</html>
