<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
    $stmt->execute([$username, $email]);
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifies if user exist, and if password hash and email match
    if ($users && password_verify($password, $users['password']) && $users['email'] === $email && $users['username'] === $username) {
        $_SESSION['password'] = $users['password'];
        $_SESSION['usertype'] = $users['usertype'];
        $_SESSION['username'] = $users['username'];
        $_SESSION['email'] = $users['email'];
    // Redirects user based on usertype (default is customer)
        switch ($users['usertype']) {
            case 'customer':
                header('Location: user_dashboard.php');
                break;
            case 'staff':
                header('Location: staff_dashboard.php');
                break;
            case 'admin':
                $_SESSION['admin'] = true;
                header('Location: admin_dashboard.php');
                break;
        }
        exit;
    } else {
        $error = 'Invalid Credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
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
            color: #5a0f1b;
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
            border-color: #7b1e2b;
            box-shadow: 0 0 0 0.2rem rgba(123, 30, 43, 0.25);
        }

        /* ===============================
           BUTTON STYLE
        ================================*/
        .btn-custom {
            background: linear-gradient(135deg, #7b1e2b, #b33a3a);
            color: #fff;
            font-weight: 500;
            padding: 12px;
            border-radius: 30px;
            transition: 0.3s ease;
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #5a0f1b, #8e2a2a);
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

                    <h3 class="brand-title text-center mb-4">Login Account</h3>

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
                            Login Account
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