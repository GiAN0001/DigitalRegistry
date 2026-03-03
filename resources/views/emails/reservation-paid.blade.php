<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        @media only screen and (max-width: 600px) {
            body { 
                width: 100% !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }
            table { 
                width: 100% !important; 
                max-width: 100% !important; 
            }
            td { 
                width: 100% !important; 
                padding: 15px !important; 
            }
            .header-td { 
                padding: 20px 15px !important; 
            }
            .body-td { 
                padding: 20px 15px !important; 
            }
            .footer-td { 
                padding: 15px !important; 
            }
            h1 { 
                font-size: 20px !important; 
            }
            h3 { 
                font-size: 14px !important; 
            }
            p { 
                font-size: 13px !important; 
            }
            .schedule-row td { 
                width: 100% !important; 
                display: block !important; 
                padding: 10px 0 !important; 
                border-bottom: 1px solid #e2e8f0 !important; 
            }
            .schedule-row td:last-child { 
                border-bottom: none !important; 
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center" style="padding: 0;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); max-width: 100%;">
                    
                    {{-- Header --}}
                    <tr>
                        <td class="header-td" style="background-color: #16a34a; padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">Payment Confirmed</h1>
                            <p style="color: #bbf7d0; margin: 8px 0 0 0; font-size: 14px;">Your payment has been received and processed</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td class="body-td" style="padding: 40px;">
                            {{-- Greeting --}}
                            <p style="color: #1e293b; font-size: 16px; margin: 0 0 20px 0;">
                                Dear <strong>{{ $reservation->renter_name ?? ($reservation->resident?->first_name . ' ' . $reservation->resident?->last_name) }}</strong>,
                            </p>
                            <p style="color: #475569; font-size: 14px; margin: 0 0 30px 0; line-height: 1.6;">
                                Thank you for your payment! We are pleased to confirm that your facility reservation payment has been successfully processed and recorded.
                            </p>

                            {{-- Status Badge --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 0 30px 0;">
                                <tr>
                                    <td style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 20px; padding: 8px 20px;">
                                        <span style="color: #15803d; font-size: 13px; font-weight: 600;">● Paid</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Event Name Only --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; margin: 0 0 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #94a3b8; font-size: 11px; margin: 0;">Event Name</p>
                                        <p style="color: #334155; font-size: 14px; margin: 2px 0 0 0; font-weight: 600;">
                                            {{ $reservation->event_name }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule Information --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; margin: 0 0 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #1e293b; font-size: 16px; margin: 0 0 15px 0; font-weight: bold;">Schedule</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr class="schedule-row">
                                                <td width="50%" style="padding: 5px 0; padding-right: 10px;">
                                                    <p style="color: #94a3b8; font-size: 11px; margin: 0;">Start Date</p>
                                                    <p style="color: #334155; font-size: 14px; margin: 2px 0 0 0; font-weight: 600;">{{ \Carbon\Carbon::parse($reservation->start_date)->format('F d, Y') }}</p>
                                                </td>
                                                <td width="50%" style="padding: 5px 0;">
                                                    <p style="color: #94a3b8; font-size: 11px; margin: 0;">End Date</p>
                                                    <p style="color: #334155; font-size: 14px; margin: 2px 0 0 0; font-weight: 600;">{{ \Carbon\Carbon::parse($reservation->end_date)->format('F d, Y') }}</p>
                                                </td>
                                            </tr>
                                            <tr class="schedule-row">
                                                <td width="50%" style="padding: 5px 0; padding-right: 10px;">
                                                    <p style="color: #94a3b8; font-size: 11px; margin: 0;">Time Start</p>
                                                    <p style="color: #334155; font-size: 14px; margin: 2px 0 0 0; font-weight: 600;">{{ \Carbon\Carbon::parse($reservation->time_start)->format('g:i A') }}</p>
                                                </td>
                                                <td width="50%" style="padding: 5px 0;">
                                                    <p style="color: #94a3b8; font-size: 11px; margin: 0;">Time End</p>
                                                    <p style="color: #334155; font-size: 14px; margin: 2px 0 0 0; font-weight: 600;">{{ \Carbon\Carbon::parse($reservation->time_end)->format('g:i A') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Equipment Information --}}
                            @if($reservation->equipments && $reservation->equipments->count() > 0)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; margin: 0 0 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #1e293b; font-size: 16px; margin: 0 0 15px 0; font-weight: bold;">Equipment Borrowed</h3>
                                        @foreach($reservation->equipments as $equipment)
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; margin: 0 0 8px 0;">
                                            <tr>
                                                <td style="padding: 10px 15px;">
                                                    <span style="color: #334155; font-size: 13px; font-weight: 500;">{{ $equipment->equipment_type }}</span>
                                                </td>
                                                <td style="padding: 10px 15px; text-align: right;">
                                                    <span style="color: #64748b; font-size: 13px;">Qty: <strong>{{ $equipment->pivot->quantity_borrowed ?? 0 }}</strong></span>
                                                </td>
                                            </tr>
                                        </table>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- Payment Information --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; margin: 0 0 30px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #15803d; font-size: 16px; margin: 0 0 15px 0; font-weight: bold;">Payment Details</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr style="border-bottom: 1px solid #86efac;">
                                                <td style="padding: 10px 0; width: 50%;">
                                                    <p style="color: #166534; font-size: 13px; margin: 0;">Amount Paid</p>
                                                </td>
                                                <td style="padding: 10px 0; text-align: right;">
                                                    <p style="color: #15803d; font-size: 14px; font-weight: 600; margin: 0;">₱{{ number_format($amountPaid, 2) }}</p>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #86efac;">
                                                <td style="padding: 10px 0;">
                                                    <p style="color: #166534; font-size: 13px; margin: 0;">Mode of Payment</p>
                                                </td>
                                                <td style="padding: 10px 0; text-align: right;">
                                                    <p style="color: #15803d; font-size: 14px; font-weight: 600; margin: 0;">{{ $modeOfPayment }}</p>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #86efac;">
                                                <td style="padding: 10px 0;">
                                                    <p style="color: #166534; font-size: 13px; margin: 0;">OR Number</p>
                                                </td>
                                                <td style="padding: 10px 0; text-align: right;">
                                                    <p style="color: #15803d; font-size: 14px; font-weight: 600; margin: 0;">{{ $orNumber }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0;">
                                                    <p style="color: #166534; font-size: 13px; margin: 0;">Date Paid</p>
                                                </td>
                                                <td style="padding: 10px 0; text-align: right;">
                                                    <p style="color: #15803d; font-size: 14px; font-weight: 600; margin: 0;">{{ now()->format('F d, Y g:i A') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Confirmation Message --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin: 0 0 30px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #15803d; font-size: 14px; font-weight: 600; margin: 0 0 8px 0;">✓ Payment Successfully Recorded</p>
                                        <p style="color: #166534; font-size: 13px; margin: 0; line-height: 1.5;">
                                            Your reservation is now confirmed and fully paid. You can proceed with your event on the scheduled date. Thank you!
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Divider --}}
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0 0 20px 0;">

                            <p style="color: #94a3b8; font-size: 12px; margin: 0; line-height: 1.5; text-align: center;">
                                If you have any questions or concerns, please don't hesitate to contact us at the barangay hall.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td class="footer-td" style="background-color: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-size: 11px; margin: 0;">
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