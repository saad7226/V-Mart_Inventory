<!DOCTYPE html>
<html>
<head>
    <title>Account Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #10b981;">Account Approved!</h2>
        <p>Hi {{ $mailData['name'] }},</p>
        <p>Great news! Your account on {{ readConfig('site_name') ?? 'V-Mart Inventory' }} has been approved by a Super Admin.</p>
        <p>You can now log in and access your store's inventory and POS system.</p>
        <div style="margin: 30px 0;">
            <a href="{{ url('/login') }}" style="background: #7c3aed; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">Login to your account</a>
        </div>
        <p>Welcome aboard!</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999;">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>
