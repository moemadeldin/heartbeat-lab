<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Welcome to {{ config('app.name') }}, {{ $name }}!</h2>

    <p>Thanks for signing up. Your account is ready to go.</p>

    <p>You can start adding sites to monitor from your dashboard.</p>

    <p>
        <a href="{{ route('dashboard') }}" style="display:inline-block;background:#3b82f6;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;">Go to Dashboard</a>
    </p>

    <p style="color:#666;font-size:12px;">If you did not create this account, you can safely ignore this email.</p>
</body>
</html>
