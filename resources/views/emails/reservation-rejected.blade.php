<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservation Rejected</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media only screen and (max-width: 600px) {
            body, table, td { width: 100% !important; }
            .main-table { width: 100% !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" class="main-table" style="background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 6px rgba(0,0,0,0.1); max-width:100%;">
                    <tr>
                        <td style="background:#dc2626; padding:30px 20px; text-align:center;">
                            <h1 style="color:#fff; margin:0; font-size:24px; font-weight:bold;">Reservation Rejected</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 20px;">
                            <p style="color:#1e293b; font-size:16px; margin:0 0 20px 0;">
                                Dear <strong>{{ $reservation->renter_name }}</strong>,
                            </p>
                            <p style="color:#475569; font-size:14px; margin:0 0 20px 0;">
                                We regret to inform you that your facility reservation request has been <strong style="color:#dc2626;">rejected</strong>.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border-radius:8px; margin:0 0 20px 0;">
                                <tr>
                                    <td style="padding:20px;">
                                        <p style="color:#94a3b8; font-size:11px; margin:0;">Event Name</p>
                                        <p style="color:#334155; font-size:14px; margin:2px 0 0 0; font-weight:600;">
                                            {{ $reservation->event_name }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <h3 style="color:#dc2626; font-size:16px; margin:0 0 10px 0; font-weight:bold;">Reason for Rejection</h3>
                            <p style="color:#b91c1c; font-size:14px; margin:0 0 20px 0;">
                                {{ $reason ?? 'No reason provided.' }}
                            </p>
                            <hr style="border:none; border-top:1px solid #e2e8f0; margin:20px 0;">
                            <p style="color:#94a3b8; font-size:12px; margin:0; text-align:center;">
                                If you have questions, please contact the barangay hall.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; padding:20px 15px; text-align:center; border-top:1px solid #e2e8f0;">
                            <p style="color:#94a3b8; font-size:11px; margin:0;">
                                This is an automated email from the Barangay Digital Registry System. Please do not reply.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>