<?php
session_start();
error_reporting(0);
require_once('connection/dbController.php');
$db_handle = new DBController();

if(isset($_SESSION['uid'])){
    ?>
    <script>
        window.location.href="index.php";
    </script>
    <?php
}

if (isset($_POST['login'])) {

    $email = $_POST['email'] ?? '';
    $pass = $_POST['pass'] ?? '';

    // Insert invoice data
    $select=$db_handle->selectQuery("select * from `user` where `email`='$email' and `pass`='$pass' and status=1");


    if($select){
        $sharableUrl=$_SESSION['invoiceURL'];
        $_SESSION['uid']=$select[0]['uid'];

        $uid = $select[0]['uid'];

        if(!empty($sharableUrl)){
            $credit = $select[0]['credit'];

            if($credit>5){
                $query="update `user` set  credit=credit-5 WHERE `uid`={$uid}";
                $update=$db_handle->insertQuery($query);

                $query = "update `invoice` set uid='$uid' WHERE `sharable_url`='$sharableUrl'";
                $update = $db_handle->insertQuery($query);

                $query = "update `invoice_detail` set uid='$uid' WHERE `isharable_url`='$sharableUrl'";
                $update = $db_handle->insertQuery($query);
            }else{
                $query = "update `invoice` set uid='$uid' and istatus=1 WHERE `sharable_url`='$sharableUrl'";
                $update = $db_handle->insertQuery($query);

                $query = "update `invoice_detail` set uid='$uid' WHERE `isharable_url`='$sharableUrl'";
                $update = $db_handle->insertQuery($query);
            }
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            Swal.fire({
                title: 'Successful',
                text: 'Login Successful.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = "createInvoice.php";
            });
        </script>
        </body>
        </html>
        <?php
    }else{
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            Swal.fire({
                title: 'Error',
                text: 'Something went wrong',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = "login.php";
            });
        </script>
        </body>
        </html>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Invoice Spark</title>
    <link href="assets/images/icon.png" rel="icon">
    <link href="assets/fonts/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
          rel="stylesheet">
    <link href="assets/css/all.min.css" rel="stylesheet">
    <link href="assets/css/slick.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/media-query.css" rel="stylesheet">
    <style>
        .upload-card {
            width: 100%;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #f8f9fa;
            transition: border-color 0.3s;
        }

        .upload-card:hover {
            border-color: #0d6efd;
        }


        .custom-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            width: 100%;
            border: 1px dashed black;
            border-radius: 5px;
            padding: 10px;
        }

        .custom-grid .form-floating {
            width: 100%;
        }

        .delete-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .custom-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .custom-grid {
                grid-template-columns: 1fr;
            }
        }

        .image-radio {
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            transition: border-color 0.3s;
        }

        .image-radio input[type="radio"] {
            display: none;
        }

        .image-radio img {
            width: 100%;
            object-fit: cover;
            margin-bottom: 5px;
        }

        .image-radio.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }

        .nav-link{
            font-size: 18px;
            font-weight: 500;
            font-family: Inter;
            color: #12151C;
            padding-right: 1.5rem !important;
        }
    </style>
</head>
<body>
<div class="site-content">
    <!-- Header Section Start -->
    <header id="header" class="bg-white shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/images/logo.png" alt="logo" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                        aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span><i class="fas fa-bars"></i></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <?php if (!isset($_SESSION['uid'])): ?>
                            <li class="nav-item">
                                <a class="nav-link demo-txt" href="index.php#pages-sec">Demos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link template-txt" href="index.php#features-sec">Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link template-txt" href="login.php">Login</a>
                            </li>
                            <li class="nav-item purchase-btn">
                                <a class="btn btn-primary ms-lg-2" href="createAccount.php">Create Account</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="editPersonalInfo.php">Edit Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="createInvoice.php">Create Invoice</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="viewInvoice.php">View Invoice</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="creditHistory.php">Credit History</a>
                            </li>
                            <li class="nav-item purchase-btn">
                                <a class="btn btn-danger ms-lg-2" href="logout.php">Log Out</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header Section End -->

    <!-- Hero Section Start -->
    <section class="hero-sec">
        <div class="container">
            <div class="hero-full-sec">
                <div class="hero-full-second pb-5">
                    <div class="card p-4 bg-light">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="container py-5">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Login</h2>
                                </div>

                                <div class="row g-4">
                                    <!-- Sender and Addresses -->
                                    <!-- Invoice Details -->
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="email" placeholder="" type="email" required>
                                                <label>Email <span style="color: red">*</span></label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="pass" placeholder="" type="password" required>
                                                <label>Password <span style="color: red">*</span></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button class="btn btn-primary w-100" type="submit" name="login">Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <p class="text-center">Don't have account? <a href="createAccount.php">Create Account</a></p>

                        <a href="forgot-password.php"><p class="text-center">Forgot Password?</p></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->



    <!-- Footer Section Start  -->
    <footer id="copyright-sec" class="fixed-bottom" style="position: fixed">
        <div class="container">
            <div class="footer-line">
                <p class="copyright-txt">Digital Invoice © Copyright by
                    <span class="footer-txt2"><a href="https://frogbid.com" target="_blank">FrogBID</a></span>
                </p>
            </div>
        </div>
    </footer>
    <!-- Footer Section End  -->

    <!--Scroll Top to Bottom Section Start -->
    <div class="scroll-top" data-scroll="up" style="">
        <svg class="border-circle svg-inner" height="100%" viewbox="-1 -1 102 102" width="100%">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
        </svg>
    </div>
    <!--Scroll Top to Bottom Section End -->
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
