<?php
session_start();
include 'include/db_connect.php'; // Make sure this file sets up $conn correctly

    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Prepare query to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                // ✅ Store session data for the logged-in admin
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "No account found with that email!";
        }
    }
    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Midnight Brew</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css?v=10">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif; 
        color: #FFD700;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .login-container {
        background: rgba(0,0,0,0.85);
        padding: 40px;
        border-radius: 15px;
        width: 100%;
        max-width: 400px;
        text-align: center;
        position: relative;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
    }

    .login-container img {
        width: 80px;
        margin-bottom: 10px;
    }

    .login-container h1 {
        margin: 0 0 20px;
        font-size: 28px;
    }

    .login-container form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .login-container input {
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid #FFD700;
        background: rgba(255,255,255,0.1);
        color: #FFD700;
        font-size: 14px;
    }

    .login-container input::placeholder {
        color: #FFD700;
    }

    .login-container button {
        padding: 10px;
        border-radius: 6px;
        border: none;
        background: #FFD700;
        color: #000;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .login-container button:hover {
        background: #e6c200;
    }

    .error {
        color: #ff4d4d;
        margin-bottom: 10px;
        font-size: 14px;
    }
    </style>
    </head>
    <body>

    <div class="login-container">
        <img src="img/logo.png" alt="Midnight Brew Logo">
        <h1>Midnight Brew Admin Login</h1>

        <?php if(isset($error)) { echo '<div class="error">'.$error.'</div>'; } ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login"><i class='bx bx-log-in'></i> Login</button>
        </form>
    </div>

    </body>
    </html>
