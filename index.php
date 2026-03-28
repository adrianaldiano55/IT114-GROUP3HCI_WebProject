<?php
session_start();
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <style>
        /* === MODIFIED: GLOBAL LAYOUT & BACKGROUND === */
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/food-bg.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            color: #fff;
        }

        /* Dark overlay as seen in Image 2 */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); 
            z-index: 0;
        }

        /* === MODIFIED: LOGO STYLE === */
        /* === MODIFIED: LOGO ALIGNMENT + TIGHT SPACING === */

        .logo-container {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            line-height: 0.15; /* tighter overall spacing */
            text-align: left; /* force left alignment */
        }

        .logo-main {
            color: #ff6600;
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 900;
            display: block;
            margin: 0;              /* remove any default spacing */
            margin-bottom: -10px;    /* 🔥 pulls subtitle upward (tight/kiss effect) */
        }

        .logo-sub {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;              /* remove spacing */
        }
                /* === MODIFIED: HERO SECTION (IMAGE 1) === */
        
        .hero-section {
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 1;
            padding: 20px;
        }

        .header-logo {
            position: absolute;
            top: 30px;
            left: 30px;
            font-size: 3rem;
            font-weight: 800;
            color: #ff0000; /* Matching 'logo' text color in image */
            margin: 0;
            line-height: 1;
        }


        .hero-title {
            font-family: sans-serif ;
            font-size: clamp(2rem, 4vw, 4.5rem);
            font-weight: 800;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
            margin-bottom: 60px;
        }

        /* === MODIFIED: ORDER BUTTON DESIGN === */
        .btn-order-now {
            background: linear-gradient(135deg, #ff7a18, #ff3d00); /* food-like orange/red */
            color: #fff;
            font-weight: 700;
            font-size: 1.6rem;
            padding: 16px 50px;
            border-radius: 20px; /* less oval, more modern */
            text-decoration: none;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            transition: 0.3s;
            display: inline-block;
        }

        .btn-order-now:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 25px rgba(0,0,0,0.4);
        }

        /* === MODIFIED: SVG ARROW STYLE === */

        .scroll-down-btn {
            position: absolute;
            bottom: 40px;
            width: 60px;  /* controls size */
            height: 60px;
            cursor: pointer;
            transition: 0.3s;
            opacity: 0; /* hidden by default */
        }

        /* Show only on hover */
        .hero-section:hover .scroll-down-btn {
            opacity: 1;
            animation: bounce 1.5s infinite;
        }

        /* SVG inside */
        .arrow-svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Hover effect */
        .scroll-down-btn:hover .arrow-svg path {
            stroke: #ff7a18; /* food color on hover */
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }

        /* === MODIFIED: LOGIN CONTAINER (IMAGE 2) === */
        .login-section {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .custom-card {
            border: none;
            border-radius: 30px; /* More rounded as per Image 2 */
            background: #f8f5f2; /* Off-white background from image */
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }

        .brand-title {
            font-weight: 800;
            color: #000000;
            font-size: 2.2rem;
        }

        /* Button Styles to match Image 2 */
        .btn-custom {
            background: linear-gradient(135deg, #ff7a18, #ff3d00); /* Solid deep red from image */
            color: #fff;
            font-weight: 600;
            padding: 14px;
            border-radius: 20px;
            transition: 0.3s ease;
            border: none;
        }

        .btn-custom:hover {
            opacity: 0.7;
            color: #fff;
        }

        .btn-outline-custom {
            border: 2px solid #ce6e00;
            color: #5a360f;
            font-weight: 600;
            padding: 14px;
            border-radius: 20px;
            transition: 0.3s ease;
            background: transparent;
        }

        .btn-outline-custom:hover {
            background-color: #000000;
            color: #fff;
        }

        @media (max-width: 576px) {
            .hero-title { font-size: 2.5rem; }
            .header-logo { font-size: 2rem; top: 15px; left: 15px; }
        }
    </style>
</head>

<body>

<section class="hero-section">
    <!-- MODIFIED: LOGO TEXT -->
        
        <h1 class="header-logo logo-container">
            <span class="logo-main">FOOD</span>
            <span class="logo-sub">AT YOUR DOORSTEP</span>
        </h1>
    
    <div class="hero-content">
        
        <!-- MODIFIED: HERO TITLE -->
        <h2 class="hero-title">From our kitchen to your table real quick.</h2>
        
        <a href="#login-ui" class="btn-order-now">Order now</a>
    </div>

    <!-- MODIFIED: SVG ARROW -->
    <a href="#login-ui" class="scroll-down-btn">
        <svg viewBox="0 0 960 960" class="arrow-svg">
            <g transform="matrix(1.09,0,0,1.09,480,374.7)">
                <path d="M-180,12 C-180,12 12,204 12,204 C12,204 198,18 198,18"
                    fill="none"
                    stroke="#ffffff"
                    stroke-width="80"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>
            </g>
        </svg>
    </a>
</section>

<section id="login-ui" class="login-section">
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
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>