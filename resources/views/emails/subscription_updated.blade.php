<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Salexo — Subscription Updated</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background:#f6f7fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:640px;border:2px solid #1E3A8A;border-radius:12px;overflow:hidden;">

                    <!-- Row 1: White Header with Logo -->
                    <tr>
                        <td style="background:#ffffff;padding:20px;text-align:center;">
                            <img src="https://salexo.in/assets/images/logo.png" alt="Salexo" width="160"
                                style="display:inline-block;max-width:160px;height:auto;border:0;">
                        </td>
                    </tr>

                    <!-- Row 2: Color Strip with Title -->
                    <tr>
                        <td
                            style="background:linear-gradient(90deg,#1D2B4F 0%, #3A6C8E 100%);padding:16px;text-align:center;color:#fff;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;font-weight:700;">
                                Subscription Updated
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background:#ffffff;padding:28px 28px 8px 28px;color:#334155;">
                            <h2 style="margin:0 0 12px 0;color:#1E3A8A;font-size:18px;">
                                Hello {{ $MailData['Order']->contact_person_name }},
                            </h2>
                            <p style="margin:0 0 12px 0;font-size:14px;line-height:1.7;">
                                Your <strong>Salexo</strong> subscription has been updated successfully.
                            </p>

                            <!-- Details Card -->
                            <div
                                style="background:#f0f4f8;border:1px solid #3B82F6;border-radius:10px;padding:16px;margin:16px 0;">
                                <p style="margin:0 0 6px 0;font-size:13px;color:#64748b;">Subscription details</p>
                                <p style="margin:6px 0;font-size:14px;"><strong>Duration Added:</strong>
                                    {{ $MailData['Days'] }} days</p>
                                <p style="margin:6px 0;font-size:14px;"><strong>Start Date:</strong>
                                    {{ $MailData['Start'] }}</p>
                                <p style="margin:6px 0;font-size:14px;"><strong>End Date:</strong>
                                    {{ $MailData['End'] }}</p>
                            </div>

                            <!-- CTA Button -->
                            <div style="margin:18px 0 6px 0;">
                                <a href="{{ $MailData['AppUrl'] }}"
                                    style="display:inline-block;background:#22C55E;color:#ffffff !important;text-decoration:none;
                  padding:12px 22px;border-radius:8px;font-weight:700;font-size:14px;
                  border:1px solid #16A34A;box-shadow:0 4px 10px rgba(34,197,94,0.25);">
                                    Go to Salexo
                                </a>
                            </div>

                            <hr style="border:none;border-top:1px solid #e3e8ef;margin:22px 0;">

                            <p style="font-size:13px;color:#475569;margin:0;">
                                Need help? Reply to this email or contact us at
                                <a href="mailto:info@salexo.in"
                                    style="color:#3B82F6;text-decoration:none;">info@salexo.in</a>.
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
