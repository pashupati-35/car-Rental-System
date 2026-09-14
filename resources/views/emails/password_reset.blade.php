<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 30px; color: #1e293b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; padding: 32px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; background: #2563eb; color: #ffffff; font-weight: 800; font-size: 20px; border-radius: 12px;">CR</div>
            <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 16px; margin-bottom: 4px;">Password Reset & Account Setup</h2>
            <p style="font-size: 13px; color: #64748b; margin: 0;">{{ $role }} Portal Security</p>
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #334155;">
            Hello <strong>{{ $recipientName }}</strong>,
        </p>

        <p style="font-size: 14px; line-height: 1.6; color: #334155;">
            You are receiving this email because a password reset or new account setup request was initiated for your <strong>{{ $role }}</strong> account. Click the button below to set a new password:
        </p>

        <div style="text-align: center; margin: 28px 0;">
            <a href="{{ $resetUrl }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; text-decoration: none; display: inline-block; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                Set / Reset Password
            </a>
        </div>

        <p style="font-size: 12px; line-height: 1.5; color: #64748b;">
            This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.
        </p>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8; word-break: break-all;">
            If you're having trouble clicking the button, copy and paste this URL into your browser:<br>
            <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
        </div>
    </div>
</body>
</html>
