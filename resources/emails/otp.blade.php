<!DOCTYPE html>
<html>

<head>
    <title>Your OTP for Affan Student Timetable</title>
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
            margin-bottom: 20px;
        }

        .otp-container {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            margin: 20px 0;
        }

        .footer {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Your One-Time Password</h2>
        </div>

        <p>Hello,</p>

        <p>Thank you for logging in to Affan Student Timetable. Please use the following OTP to verify your account:</p>

        <div class="otp-container">
            {{ $otp }}
        </div>

        <p>This OTP will expire in 10 minutes.</p>

        <p>If you did not request this OTP, please ignore this email or contact support if you have concerns.</p>

        <p>Best regards,<br>
            Affan Student Timetable Team</p>

        <div class="footer">
            This is an automated message. Please do not reply to this email.
        </div>
    </div>
</body>

</html>
