<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .otp-container {
            text-align: center;
            margin: 30px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #007bff;
            padding: 10px;
            background-color: #e9f5ff;
            border-radius: 5px;
            display: inline-block;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .verify-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>

        <p>Hello,</p>

        <p>We received a request to reset your password. Please use the following One-Time Password (OTP) to proceed
            with your password reset:</p>

        <div class="otp-container">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p>Click the button below to enter your OTP and reset your password:</p>

        <div class="button-container">
            <a href="{{ url('verify-otp') }}" style="color: white;" class="verify-button">Verify OTP</a>
        </div>

        <p>Or you can enter the OTP manually on the verification page.</p>

        <p>This OTP will expire in 15 minutes. If you did not request a password reset, please ignore this email or
            contact support if you have concerns.</p>

        <p>Thank you,<br>
            Affan Student Timetable Team</p>

        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
