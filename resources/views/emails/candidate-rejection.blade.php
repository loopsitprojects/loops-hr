<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!--<![endif]-->
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff !important;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }
        .header {
            background-color: #0f172a; /* brand-navy */
            padding: 24px 30px;
            text-align: center;
            border-bottom: 4px solid #0d9488; /* brand-teal accent */
        }
        .content {
            padding: 48px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .message-body {
            font-size: 15px;
            line-height: 1.8;
            color: #1e293b;
            margin-bottom: 32px;
        }
        .footer {
            padding: 32px;
            text-align: center;
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
        .footer-text {
            color: #64748b;
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }
        .signature {
            border-left: 2px solid #0d9488;
            padding-left: 20px;
            margin-top: 48px;
        }
        .signature p {
            margin: 0;
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
        }
        .signature strong {
            color: #0f172a;
            font-size: 15px;
        }
        .hidden-ref {
            display: none !important;
            visibility: hidden;
            mso-hide: all;
            font-size: 1px;
            color: #f8fafc;
        }
        @media only screen and (max-width: 600px) {
            .content { padding: 24px; }
            .container { margin: 10px; border-radius: 12px; }
            .brand-logo { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center" style="padding: 12px 0;">
                            <img src="https://ai.loopsintegrated.co/logo/LoopsWhite.png" height="60" alt="Loops Integrated" style="display: block; border: 0;">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="content">
                <div class="message-body">
                    @if(isset($customMessage) && !empty($customMessage))
                        {!! nl2br(e($customMessage)) !!}
                    @else
                        <p>Dear {{ $candidateName }},</p>
                        <p>Thank you for your interest in joining the team at Loops Integrated and for the time you invested in your application.</p>
                        <p>After careful consideration, we have decided not to move forward with your application for this position at this time. Our decision was made based on our current requirements and the high volume of applications we received.</p>
                        <p>We appreciate the opportunity to review your profile and wish you the very best in your future endeavors. Your details will be kept in our database for future opportunities that may be a better match for your skill set.</p>
                    @endif
                </div>

                <div class="signature">
                    <p>Best regards,</p>
                    <p><strong>The Recruitment Team</strong></p>
                    <p>Loops Integrated</p>
                </div>
            </div>
            <div class="footer">
                <p class="footer-text">
                    &copy; {{ date('Y') }} Loops Integrated (Pvt) Ltd. All rights reserved.<br>
                    Colombo, Sri Lanka
                </p>
                <div class="hidden-ref">Reference ID: {{ uniqid() }}</div>
            </div>
        </div>
    </div>
</body>
</html>
