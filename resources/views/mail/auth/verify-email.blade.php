<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Verify your email</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;color:#172033;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">Confirm your email to activate your Verified Shortlet account.</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f6f8;">
    <tr>
        <td align="center" style="padding:32px 16px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;">
                <tr>
                    <td style="padding:0 4px 20px;">
                        <a href="{{ $homeUrl }}" style="color:#172033;text-decoration:none;font-size:20px;font-weight:700;letter-spacing:-0.3px;">
                            <span style="display:inline-block;width:34px;height:34px;line-height:34px;margin-right:9px;border-radius:10px;background:#e85d2a;color:#ffffff;text-align:center;font-size:17px;vertical-align:middle;">VS</span>
                            <span style="vertical-align:middle;">Verified Shortlet</span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="overflow:hidden;border:1px solid #e2e7ed;border-radius:24px;background:#ffffff;box-shadow:0 12px 35px rgba(23,32,51,0.08);">
                        <div style="height:6px;background:#e85d2a;"></div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="padding:42px 42px 36px;">
                                    <div style="width:52px;height:52px;line-height:52px;border-radius:16px;background:#fff2ec;color:#d94f1f;text-align:center;font-size:25px;font-weight:700;">✓</div>
                                    <p style="margin:26px 0 8px;color:#e05a2a;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">One quick step</p>
                                    <h1 style="margin:0;color:#172033;font-size:30px;line-height:1.2;font-weight:700;letter-spacing:-0.7px;">Welcome, {{ $firstName }}</h1>
                                    <p style="margin:16px 0 0;color:#536174;font-size:16px;line-height:1.7;">Confirm your email address to secure your account and continue your Verified Shortlet journey.</p>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0 24px;">
                                        <tr>
                                            <td align="center" style="border-radius:12px;background:#172033;">
                                                <a href="{{ $verificationUrl }}" style="display:inline-block;padding:15px 24px;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;line-height:1;">Verify my email</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:0;color:#7a8798;font-size:13px;line-height:1.6;">For your security, this link expires in {{ $expiresInMinutes }} minutes. If you did not create this account, you can safely ignore this email.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:22px 42px;border-top:1px solid #edf0f3;background:#fafbfc;">
                                    <p style="margin:0 0 7px;color:#536174;font-size:12px;font-weight:700;">Button not working?</p>
                                    <p style="margin:0;color:#7a8798;font-size:11px;line-height:1.55;word-break:break-all;">Copy this link into your browser:<br><a href="{{ $verificationUrl }}" style="color:#d94f1f;text-decoration:underline;">{{ $verificationUrl }}</a></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:22px 20px 0;color:#8a95a4;font-size:11px;line-height:1.6;">
                        Sent securely by Verified Shortlet<br>
                        Find trusted stays. Host with confidence.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
