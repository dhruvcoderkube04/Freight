<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 20px; }
        .button { background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </p>
            
            <p>This password reset link will expire in 60 minutes.</p>
            
            <p>If you did not request a password reset, no further action is required.</p>
            
            <p>Thank you,<br>{{ config('app.name') }}</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>