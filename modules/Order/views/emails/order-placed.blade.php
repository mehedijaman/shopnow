<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Order #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 620px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #1a202c; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 13px; color: #a0aec0; }
        .body { padding: 32px; }
        .section-title { font-size: 14px; font-weight: bold; color: #718096; text-transform: uppercase; letter-spacing: .05em; margin: 24px 0 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .info-row span:first-child { color: #718096; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 8px; }
        th { background: #f7fafc; text-align: left; padding: 8px 10px; font-size: 12px; text-transform: uppercase; color: #718096; }
        td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
        .total-row td { font-weight: bold; border-bottom: none; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .footer { background: #f7fafc; padding: 16px 32px; font-size: 12px; color: #a0aec0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Order Received</h1>
            <p>Order #{{ $order->id }} &mdash; {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <div class="body">
            <p style="font-size:15px;">A new order has been placed on your store. Please review the details below.</p>

            <div class="section-title">Customer Details</div>
            <div class="info-row"><span>Name</span><span>{{ $order->name }}</span></div>
            <div class="info-row"><span>Phone</span><span>{{ $order->phone }}</span></div>
            @if ($order->email)
                <div class="info-row"><span>Email</span><span>{{ $order->email }}</span></div>
            @endif
            @if ($order->address)
                <div class="info-row"><span>Address</span><span>{{ $order->address }}</span></div>
            @endif
            <div class="info-row">
                <span>Payment Method</span>
                <span>{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ($order->payment_method ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span>Status</span>
                <span><span class="badge badge-pending">{{ ucfirst($order->status) }}</span></span>
            </div>

            <div class="section-title">Ordered Items</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Unit Price</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderProducts as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'Product #'.$item->product_id }}</td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td style="text-align:right;">{{ number_format($item->unit_price, 2) }} Tk</td>
                            <td style="text-align:right;">{{ number_format($item->total_price, 2) }} Tk</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; color:#718096;">Subtotal</td>
                        <td style="text-align:right;">{{ number_format($order->subtotal, 2) }} Tk</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right; color:#718096;">Shipping</td>
                        <td style="text-align:right;">{{ number_format($order->shipping, 2) }} Tk</td>
                    </tr>
                    @if ($order->tax > 0)
                        <tr>
                            <td colspan="3" style="text-align:right; color:#718096;">Tax</td>
                            <td style="text-align:right;">{{ number_format($order->tax, 2) }} Tk</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right;">Total</td>
                        <td style="text-align:right;">{{ number_format($order->total, 2) }} Tk</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            This is an automated notification from {{ setting('branding.site_name', config('app.name')) }}.
        </div>
    </div>
</body>
</html>
