<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0a0a0a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; padding: 20px; margin: 40px auto; background-color: #141414; border: 1px solid #262626; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);">

    <tr>
        <td style="padding: 40px 0 20px 0; text-align: center;">
            <div style="display: inline-block; width: 50px; height: 50px;">
                @include('components.logo')
            </div>
        </td>
    </tr>

    <tr>
        <td style="padding: 0 40px 40px 40px;">
            <p style="color: #a3a3a3; font-size: 16px; line-height: 24px; margin-bottom: 30px;">
                Hello <span style="color: #ffffff; font-weight: 600;">@{{ $username }}</span>,<br>
                Welcome to the panel! Please tap the green button below to verify your account and get started.
            </p>

            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                <tr>
                    <td style="border-radius: 8px; background-color: #1ed760;">
                        <a href="{{ $url }}" target="_blank" style="border: 1px solid #1ed760; border-radius: 8px; color: #000000; display: inline-block; font-size: 16px; font-weight: 700; padding: 14px 32px; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                            Verify Email Address
                        </a>
                    </td>
                </tr>
            </table>

            <p style="color: #737373; font-size: 13px; margin-top: 40px; line-height: 20px;">
                If the button above doesn't work, copy and paste this link into your browser:<br>
                <a href="{{ $url }}" style="color: #1ed760; text-decoration: none; word-break: break-all;">{{ $url }}</a>
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding: 30px 40px; background-color: #1c1c1c; text-align: center; border-top: 1px solid #262626;">
            <p style="color: #525252; font-size: 12px; margin: 0; margin-bottom: 8px;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
            <p style="color: #525252; font-size: 12px; margin: 0;">
                You received this email because you signed up for our open-source panel.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
