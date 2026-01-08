<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f7f7f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">

    <tr>
        <td style="padding: 40px 0 20px 0; text-align: center;">
            <span style="display:inline-flex;align-items:center;gap:10px;">
                <img src="https://github.com/Tsrgtm/lara-flow/tree/main/public/images/logo/icon.svg" width="32" height="32" alt="Lara Flow Logo">
                <strong style="font-size:20px;font-weight:900;color:#333;">Lara</strong><strong style="font-size:20px;font-weight:900;color:#F9322C;">Flow</strong>
            </span>
        </td>
    </tr>

    <tr>
        <td style="padding: 0 40px 40px 40px;">
            <p style="color: #374151; font-size: 16px; line-height: 24px; margin-bottom: 30px;">
                Hello <span style="color: #111827; font-weight: 600;">@{{ $username }}</span>,<br>
                Welcome to the <b>Lara Flow</b> panel! Please tap the green button below to verify your account and get started.
            </p>

            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                <tr>
                    <td style="border-radius: 8px; background-color: #10b981;">
                        <a href="{{ $url }}" target="_blank" style="border: 1px solid #10b981; border-radius: 8px; color: #ffffff; display: inline-block; font-size: 16px; font-weight: 600; padding: 14px 32px; text-decoration: none; letter-spacing: 0.5px;">
                            Verify Email Address
                        </a>
                    </td>
                </tr>
            </table>

            <p style="color: #6b7280; font-size: 13px; margin-top: 40px; line-height: 20px;">
                If the button above doesn't work, copy and paste this link into your browser:<br>
                <a href="{{ $url }}" style="color: #10b981; text-decoration: none; word-break: break-all;">{{ $url }}</a>
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding: 30px 40px; background-color: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 12px; margin: 0; margin-bottom: 8px;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
            <p style="color: #6b7280; font-size: 12px; margin: 0;">
                You received this email because you signed up for our open-source panel.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
