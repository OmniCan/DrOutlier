<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            position: relative;
            overflow: hidden;
        }
        .container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(45deg, #a075ee, #ec601f);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }


    </style>
</head>
<body>

    <div class="container">
        <h1>Hello {{ $user->firstname }},</h1>
        <p>Thank you for registering. Please click the button below to verify your email address:</p>
        <a href="{{ $verificationUrl }}" class="button">Verify Email</a>
    </div>
</body>
</html>
