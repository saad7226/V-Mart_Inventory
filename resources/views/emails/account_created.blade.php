<!DOCTYPE html>
<html>
<head>
    <title>Account Created - Pending Approval</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #7c3aed;">Welcome to {{ readConfig('site_name') ?? 'V-Mart Inventory' }}!</h2>
        <p>Hi {{ $mailData['name'] }},</p>
        <p>You have successfully created your account on V-Mart Inventory.</p>
        <p><strong>Note:</strong> Your account is currently pending approval from a Super Admin. You will receive another email once your account has been approved and you can log in.</p>
        <p>Thank you for registering!</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999;">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>
