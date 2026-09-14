<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Status Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #4f46e5, #3b82f6); color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
        .content { padding: 28px; line-height: 1.6; font-size: 14px; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 12px; text-transform: uppercase; }
        .badge-confirm { background: #dcfce7; color: #166534; }
        .badge-cancel { background: #fee2e2; color: #991b1b; }
        .details-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin: 18px 0; }
        .details-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
        .details-label { color: #64748b; font-weight: 600; }
        .details-value { color: #0f172a; font-weight: 700; }
        .footer { padding: 18px 28px; background: #f1f5f9; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>AutoRent Reservation Desk</h1>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $booking->customer->name ?? $booking->name ?? 'Valued Customer' }}</strong>,</p>
            
            <p>
                We are writing to update you on your car rental reservation with AutoRent.
            </p>

            <div style="text-align: center; margin: 16px 0;">
                <span class="badge {{ $status === 'confirm' ? 'badge-confirm' : 'badge-cancel' }}">
                    Reservation #BK-{{ $booking->id }}: {{ strtoupper($status) }}ED
                </span>
            </div>

            <div class="details-box">
                <div class="details-row">
                    <span class="details-label">Reserved Vehicle:</span>
                    <span class="details-value">{{ $booking->car->car_name ?? 'Vehicle' }} {{ $booking->car->car_model ?? '' }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Rental Duration:</span>
                    <span class="details-value">{{ $booking->start_date }} &rarr; {{ $booking->end_date }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Total Rental Cost:</span>
                    <span class="details-value">${{ $booking->total_price ?? $booking->amount ?? 0 }}</span>
                </div>
                @if($booking->car && $booking->car->driver)
                <div class="details-row">
                    <span class="details-label">Assigned Driver:</span>
                    <span class="details-value">{{ $booking->car->driver->name }} ({{ $booking->car->driver->phone }})</span>
                </div>
                @endif
            </div>

            @if($status === 'confirm')
                <p style="color: #166534; font-weight: 600;">
                    ✓ Your booking is confirmed! Your vehicle will be prepped and ready on your scheduled pick-up date.
                </p>
            @else
                <p style="color: #991b1b; font-weight: 600;">
                    Your booking reservation has been cancelled. Any eligible deposits or payments will be refunded per rental policies.
                </p>
            @endif

            <p style="margin-top: 24px;">
                Thank you for choosing AutoRent. For emergency assistance or schedule changes, please contact support.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AutoRent Global Fleet Systems. All rights reserved.
        </div>
    </div>
</body>
</html>
