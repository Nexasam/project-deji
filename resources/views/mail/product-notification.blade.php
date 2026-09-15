<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $notificationTitle }}</title></head>
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a">
<div style="max-width:600px;margin:0 auto;padding:32px 16px">
    <div style="border-radius:18px;background:#fff;padding:28px;border:1px solid #e2e8f0">
        <p style="margin:0 0 8px;color:#ea580c;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em">Verified Shortlet</p>
        <h1 style="margin:0;font-size:24px">{{ $notificationTitle }}</h1>
        <p style="margin:18px 0 0;line-height:1.65;color:#475569">{{ $notificationMessage }}</p>
        @if($actionUrl)<p style="margin:24px 0 0"><a href="{{ $actionUrl }}" style="display:inline-block;border-radius:10px;background:#0f172a;color:#fff;padding:12px 18px;text-decoration:none;font-weight:700">View details</a></p>@endif
    </div>
</div>
</body>
</html>
