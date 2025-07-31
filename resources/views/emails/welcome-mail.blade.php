<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Welcome to Our Store</title>
  <style>
    body {
      background-color: #f6f6f6;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .email-container {
      background-color: #ffffff;
      width: 90%;
      max-width: 600px;
      margin: 30px auto;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .header {
      text-align: center;
      border-bottom: 1px solid #eaeaea;
      padding-bottom: 20px;
      margin-bottom: 20px;
    }

    .header h1 {
      color: #333;
    }

    .content {
      font-size: 16px;
      color: #444;
      line-height: 1.6;
    }

    .button {
      display: inline-block;
      background-color: #0d6efd;
      color: #fff;
      padding: 12px 20px;
      margin-top: 20px;
      text-decoration: none;
      border-radius: 5px;
    }

    .footer {
      margin-top: 30px;
      font-size: 13px;
      color: #999;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="email-container">
    <div class="header">
      <h1>Welcome to Qutoof shop!</h1>
    </div>

    <div class="content">
      <p>Hi <strong>{{ $user->name }}</strong>,</p>

      <p>We're so excited to have you on board. 🎉</p>

      <p>Thank you for registering at <strong>Qutoof shop</strong>. Now you can enjoy shopping from a wide selection of products, track your orders, and more!</p>

      <p>Click the button below to start shopping:</p>

      <a href="{{url('api/e_commerce/home')}}" class="button">Visit Our Store</a>

      <p>If you have any questions, feel free to reach out to us anytime.</p>

      <p>Happy Shopping!<br>
      The Qutoof shop Team</p>
    </div>

    <div class="footer">
      &copy; 2025 Qutoof shop. All rights reserved.
    </div>
  </div>

</body>
</html>
