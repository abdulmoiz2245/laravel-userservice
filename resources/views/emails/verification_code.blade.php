<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 15px 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .verification-code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Thank you for registering. Your verification code is:</p>
            <p class="verification-code">{{ $verificationCode }}</p>
            <p>Please use this code to verify your email address.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
