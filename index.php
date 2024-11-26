<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Student Attendance Management System</title>

    <style>
        .topbar {
            width: 100%;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: #fff;
            text-align: center;
            padding: 20px 0;
            font-size: 50px;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 500;
        }

        /* Hero section styles */
        .hero {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 10px); /* Adjusted height to account for top bar height */
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            text-align: center;
            margin-top: 140px; /* Push the hero section below the top bar */
            overflow: hidden;
            font-weight: bold;
        }

        /* Pseudo-element for the blurred background image */
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('img/home.jpg') no-repeat center center/cover;
            background-size: cover;
            filter: blur(3px); /* Blur effect on background */
            z-index: 0;
        }

        /* Content inside the hero section */
        .hero-content {
            position: relative;
            z-index: 1;
            margin-top: 10px;
        }

        .hero-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero-content a {
            text-decoration: none;
            background-color: #fff;
            color: #333;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s;
            margin: 10px;
        }

        .hero-content a:hover {
            background-color: plum;
        }

        body {
            margin: 0;
            padding: 0;
        }

    </style>
</head>
<body>
    <!-- Top bar -->
    <div class="topbar">
        Welcome to the Online Student Attendance Management System
    </div>

    <!-- Hero section with blurred background image -->
    <div class="hero">
        <!-- Content inside hero section -->
        <div class="hero-content">
            <a href="login/login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
