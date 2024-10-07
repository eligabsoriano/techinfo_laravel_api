<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .otp {
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
            text-align: center;
            padding: 10px;
            border: 2px dashed #4CAF50;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset Request</h1>
        <p>Hello,</p>
        <p>Your OTP for resetting the password is:</p>
        <div class="otp">{{ $otp }}</div>
        <p>Please use this OTP to proceed with your password reset.</p>
        <footer>
            <p>If you did not request a password reset, please ignore this email.</p>
        </footer>
    </div>
</body>
</html>
