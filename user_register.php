<?php
require_once 'config.php';

// Handle User Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        // Verifies if email is a valid Gmail address
        if (preg_match('/@gmail.com$/', $_POST['email'])) {
        } else {
            die("Invalid email");
        }
        $email = $_POST['email'] ?? '';
        // Verifies if password is a strong password (lowercase,uppercase,number etc.)
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $_POST['password'])) {
        }else{
            die('Password must be at least 8 characters long and include uppercase, lowercase, and a number.');
        }
        // Hashes password before storing into Database
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT) ?? '';
        $usertype = $_POST['usertype'] ?? 'customer';
        // Preps SQL for database storage
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, usertype) VALUES (?, ?, ?, ?)");
        // Inserts SQL data into database
        $stmt->execute([$username, $email, $password, $usertype]);
        // Redirects to login page after successful registration
        header('Location: user_login.php');
        exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ===============================
           BACKGROUND
        ================================*/
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
            background: url('images/food-bg.jpg') no-repeat center center/cover;
        }

        /* Dark Overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            z-index: -1;
        }

        /* ===============================
           CARD DESIGN
        ================================*/
        .custom-card {
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            transition: 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-5px);
        }

        .brand-title {
            font-weight: 700;
            color: #000000;
        }

        /* ===============================
           INPUT STYLING
        ================================*/
        .form-control {
            border-radius: 12px;
            padding: 12px;
            transition: 0.3s ease;
        }

        .form-control:focus {
            border-color: #cf7207;
            box-shadow: 0 0 0 0.2rem rgba(123, 30, 43, 0.25);
        }

        /* ===============================
           BUTTON STYLE
        ================================*/
        .btn-custom {
            background: linear-gradient(135deg, #ff7a18, #ff3d00);
            color: #fff;
            font-weight: 500;
            padding: 12px;
            border-radius: 30px;
            transition: 0.3s ease;
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #cd6300, #000000);
            transform: scale(1.03);
            color: #fff;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">

            <div class="card custom-card">
                <div class="card-body p-5">

                    <h3 class="brand-title text-center mb-4">Create Account</h3>

                    <form method="post">
                        <input type="hidden" name="create" value="1">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-custom w-100">
                            Create Account
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>