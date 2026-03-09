<?php
// index.php
require_once 'config.php';

// If already logged in as admin, go straight to admin dashboard
if (!empty($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}
// If already logged in as staff, go straight to staff dashboard
if (!empty($_SESSION['staff']) && $_SESSION['admin'] === false) {
    header('Location: staff_dashboard.php');
    exit;
}
// If already logged in as customer, go straight to customer dashboard
if (!empty($_SESSION['customer']) && $_SESSION['admin'] === false) {
    header('Location: customer_dashboard.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food At Your DoorStep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
            background: url('images/food-bg.jpg') no-repeat center center/cover;
        }

        
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

        .btn-outline-custom {
            border: 2px solid #7b1e2b;
            color: #7b1e2b;
            font-weight: 500;
            padding: 12px;
            border-radius: 30px;
            transition: 0.3s ease;
        }

        .btn-outline-custom:hover {
            background-color: #7b1e2b;
            color: #fff;
            transform: scale(1.03);
        }


        @media (max-width: 576px) {
            .custom-card {
                padding: 10px;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card custom-card text-center">
                <div class="card-body p-5">

                    <h1 class="brand-title mb-3">Food At Your DoorStep</h1>
                    <p class="text-muted mb-4">Fresh meals delivered fast & hot</p>

                    <a class="btn btn-custom w-100 mb-3" href="user_login.php">
                        Login
                    </a>

                    <a class="btn btn-outline-custom w-100" href="user_register.php">
                        Register
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
cdn.jsdelivr.net
