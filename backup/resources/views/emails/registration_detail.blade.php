<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Welcome to Salexo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @media only screen and (max-width: 600px) {
            .btn {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
            }

            .content {
                padding: 22px !important;
            }
        }
    </style>
</head>

<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background:#f6f7fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:640px;border:2px solid #1D2B4F;border-radius:12px;overflow:hidden;">

                    <!-- Row 1: White header with Logo -->
                    <tr>
                        <td style="background:#ffffff;padding:20px;text-align:center;">
                            <img src="https://salexo.in/assets/images/logo.png" alt="Salexo" width="160"
                                style="display:inline-block;max-width:160px;height:auto;border:0;">
                        </td>
                    </tr>

                    <!-- Row 2: Color Strip -->
                    <tr>
                        <td
                            style="background:linear-gradient(90deg,#1D2B4F 0%, #3A6C8E 100%);padding:16px;text-align:center;color:#fff;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;font-weight:700;">
                                Welcome to Salexo
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background:#ffffff;padding:28px 28px 8px 28px;color:#334155;">
                            <h2 style="margin:0 0 12px 0;color:#1D2B4F;font-size:18px;">
                                Hello {{ $MailData['Order']->contact_person_name }},
                            </h2>
                            <p style="margin:0 0 12px 0;font-size:14px;line-height:1.7;">
                                Thanks for choosing <strong>Salexo</strong> — your account is ready. Here are your login
                                details:
                            </p>

                            <!-- Credentials Box -->
                            <div
                                style="background:#f0f4f8;border:1px solid #3A6C8E;border-radius:10px;padding:16px;margin:16px 0;">
                                <p style="margin:0 0 6px 0;font-size:13px;color:#64748b;">Your credentials</p>
                                <p style="margin:6px 0;font-size:14px;"><strong>User ID:</strong>
                                    {{ $MailData['Order']->mobile }}</p>
                                <p style="margin:6px 0;font-size:14px;"><strong>Password:</strong>
                                    {{ $MailData['Password'] }}</p>
                                <p style="margin:10px 0 0 0;font-size:12px;color:#6b7280;">
                                    ⚠️ Please change your password after first login for security.
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <div style="margin:18px 0 6px 0;">
                                <a href="{{ config('app.app_url') ?? url('/') }}"
                                    style="display:inline-block;background:#3A6C8E;color:#ffffff !important;text-decoration:none;
                  padding:12px 22px;border-radius:8px;font-weight:700;font-size:14px;
                  border:1px solid #1D2B4F;box-shadow:0 4px 10px rgba(58,108,142,0.25);">
                                    Login to Salexo
                                </a>
                            </div>

                            <hr style="border:none;border-top:1px solid #e3e8ef;margin:22px 0;">

                            <p style="font-size:13px;color:#475569;margin:0;">
                                If you need help, reply to this email or contact us at
                                <a href="mailto:info@salexo.in"
                                    style="color:#3A6C8E;text-decoration:none;">info@salexo.in</a>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background:#f9fafb;padding:16px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e3e8ef;">
                            &copy; {{ date('Y') }} Salexo. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
