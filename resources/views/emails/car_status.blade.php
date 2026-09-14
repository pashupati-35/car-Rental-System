<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vehicle Status Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #4f46e5, #3b82f6); color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
        .content { padding: 28px; line-height: 1.6; font-size: 14px; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 12px; text-transform: uppercase; }
        .badge-verified { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
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
            <h1>AutoRent Fleet Management</h1>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $car->owner->full_name ?? 'Fleet Partner' }}</strong>,</p>
            
            <p>
                We are writing to update you on the inspection and listing status of your vehicle registered with AutoRent.
            </p>

            <div style="text-align: center; margin: 16px 0;">
                <span class="badge {{ $status === 'verified' ? 'badge-verified' : 'badge-rejected' }}">
                    Status: {{ strtoupper($status) }}
                </span>
            </div>

            <div class="details-box">
                <div class="details-row">
                    <span class="details-label">Vehicle Name:</span>
                    <span class="details-value">{{ $car->car_name ?? $car->brand }} {{ $car->car_model ?? $car->model }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">License Plate:</span>
                    <span class="details-value">{{ $car->car_number ?? $car->plate_number ?? 'N/A' }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Daily Rental Rate:</span>
                    <span class="details-value">${{ $car->car_price_per_day ?? $car->price_per_day ?? 0 }} / day</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Seating & Fuel:</span>
                    <span class="details-value">{{ $car->number_of_seats ?? 5 }} Seats &bull; {{ ucfirst($car->fuel_type ?? 'Petrol') }}</span>
                </div>
            </div>

            @if($status === 'verified')
                <p style="color: #166534; font-weight: 600;">
                    ✓ Your car is now live and available for customer bookings on the public fleet portal.
                </p>
            @else
                <p style="color: #991b1b; font-weight: 600;">
                    Your vehicle listing has been reviewed and marked as rejected. Please check vehicle documents or contact super administration.
                </p>
            @endif

            <p style="margin-top: 24px;">
                If you have any questions or require modifications, please log in to your fleet owner portal.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AutoRent Global Fleet Systems. All rights reserved.
        </div>
    </div>
</body>
</html>
