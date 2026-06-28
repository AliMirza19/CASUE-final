<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            background-color: #0d0b21;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #e2e8f0;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #15132e;
            border: 1px solid #3b2b85;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed, #4c1d95);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header p {
            color: #ddd6fe;
            margin: 5px 0 0 0;
            font-size: 11px;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .content {
            padding: 40px 35px;
        }
        .content h2 {
            color: #ffffff;
            margin-top: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .content p {
            font-size: 15px;
            line-height: 1.6;
            color: #cbd5e1;
        }
        .button-wrapper {
            text-align: center;
            margin: 35px 0;
        }
        .btn-reset {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
            transition: all 0.3s ease;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.6);
        }
        .note {
            font-size: 13px;
            color: #94a3b8;
            border-left: 3px solid #7c3aed;
            padding-left: 15px;
            margin: 25px 0;
        }
        .footer {
            background-color: #0b091c;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #1f1a45;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }
        .footer a {
            color: #a78bfa;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #24204b;
            margin: 25px 0;
        }
        .link-text {
            word-break: break-all;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo / Header -->
        <div class="header">
            <h1>CAUSE SOCIETY</h1>
            <p>SMART SOCIETY MANAGEMENT SYSTEM</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello, {{ $name }}!</h2>
            <p>We received a request to reset your password for your account on the <strong>CAUSE Society Portal</strong>.</p>
            
            <p>Please click the button below to choose a new password. This process is secure and will expire shortly.</p>
            
            <div class="button-wrapper">
                <a href="{{ $url }}" class="btn-reset" target="_blank">Reset Password</a>
            </div>

            <div class="note">
                <strong>Expiration Warning:</strong> This password reset link will expire in 60 minutes.
            </div>

            <p>If you did not request a password reset, no further action is required; your account remains completely secure.</p>
            
            <div class="divider"></div>
            
            <p class="link-text">
                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
                <a href="{{ $url }}" style="color: #a78bfa;">{{ $url }}</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} CAUSE Smart Society. All rights reserved.</p>
            <p>Capital University of Science & Technology (CUST), Islamabad.</p>
        </div>
    </div>
</body>
</html>
