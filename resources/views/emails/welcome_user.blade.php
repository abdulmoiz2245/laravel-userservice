<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Platform</title>
    <style>
        /* Styles for email content */
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
        .welcome-message {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        .user-name {
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
        /* Logo styles */
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Platform</h1>
        </div>
        <div class="content">
            <div class="logo">
                <img src="{{ asset('images/logo/Myfreelancer-logo-white.jpeg') }}" alt="Logo">
            </div>
            <p class="welcome-message">Dear {{ $userName }},</p>
            <p class="user-name">Welcome to our platform!</p>
            <p>We are excited to have you as a new member of our community. You can now access all the features and services our platform has to offer.</p>
            <p>Thank you for joining us!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
