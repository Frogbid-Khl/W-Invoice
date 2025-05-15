<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

if(!isset($_SESSION['uid'])){
    ?>
    <script>
        window.location.href="index.php";
    </script>
    <?php
}

if (isset($_POST['createAccount'])) {

    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $terms = $_POST['terms'] ?? '';

    $inserted_at = $updated_at = date("Y-m-d H:i:s");
    $sharable_url = generateRandomString(20);


    // Logo Upload
    $logo = '';
    if (!empty($_FILES['logo']['name'])) {
        $RandomAccountNumber = mt_rand(1, 99999);
        $file_name = $RandomAccountNumber . "_" . basename($_FILES['logo']['name']);
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($file_tmp, "invoiceassets/logo/" . $file_name)) {
                $logo = "invoiceassets/logo/" . $file_name;
            }
        }
    }

    // Signature Upload
    $signature = '';
    if (!empty($_FILES['signature']['name'])) {
        $RandomAccountNumber = mt_rand(1, 99999);
        $file_name = $RandomAccountNumber . "_" . basename($_FILES['signature']['name']);
        $file_tmp = $_FILES['signature']['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($file_tmp, "invoiceassets/signature/" . $file_name)) {
                $signature = "invoiceassets/signature/" . $file_name;
            }
        }
    }

    // Insert invoice data
    $insert=$db_handle->insertQuery("INSERT INTO `user`(`name`, `address`, `logo`, `email`, `pass`, `signature`, `toc`, `credit`, `status`, `inserted_at`, `updated_at`) VALUES ('$name','$address','$logo','$email','$pass','$signature','$terms','0','1','$inserted_at','$updated_at')");


    if($insert){
        ?>
        <script>
            alert('Signup Successful. Now Verify Email and Login.');
            window.location.href="editPersonalInfo.php";
        </script>
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
    <title>Digital Invoica</title>
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
    </style>
</head>
<body>
<div class="site-content">
    <!-- Header Section Start -->
    <header id="header">
        <div class="container">
            <div class="header-full">
                <div class="logo-sec">
                    <div>
                        <a href="#"><img alt="logo" src="assets/images/logo.png"></a>
                    </div>
                </div>
                <div class="logo-sec-details">
                    <div class="menu-sec">
                        <ul class="menu-sec-details">
                            <?php
                            if(!isset($_SESSION['uid'])){
                                ?>
                                <li class="demo-txt"><a href="index.php#pages-sec">Demos</a></li>
                                <li class="template-txt"><a href="index.php#features-sec">Features</a></li>
                                <li class="template-txt"><a href="login.php">Login</a></li>
                                <li class="purchase-btn"><a href="createAccount.php">Create Account</a></li>
                                <?php
                            }else{
                                ?>
                                <li class="demo-txt"><a href="editPersonalInfo.php">Edit Info</a></li>
                                <li class="template-txt"><a href="createInvoice.php">Create Invoice</a></li>
                                <li class="template-txt"><a href="viewInvoice.php">View Invoice</a></li>
                                <li class="purchase-btn"><a href="logout.php">Log Out</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header Section End -->

    <!-- Hero Section Start -->
    <section class="hero-sec">
        <div class="container">
            <div class="hero-full-sec">
                <div class="hero-full-second pb-5 pt-5">
                    <div class="card p-4 bg-light">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="container py-5">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Edit Personal Info</h2>
                                </div>

                                <div class="row g-4">
                                    <!-- Sender and Addresses -->
                                    <!-- Invoice Details -->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="name" placeholder="" type="text">
                                                <label>Name</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="address" placeholder="" type="text">
                                                <label>Address</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="email" placeholder="" type="text">
                                                <label>Email</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="pass" placeholder="" type="password">
                                                <label>Password</label>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="logo">Upload Logo</label>
                                            <div class="upload-card">
                                                <input class="form-control border-0" name="logo" id="logo" style="width: 90%;"
                                                       type="file">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mt-4">
                                        <div class="form-floating">
                                            <textarea class="form-control" name="terms" id="terms" placeholder="Leave a comment here"
                                                      style="height: 290px"></textarea>
                                            <label for="terms">Terms and Condition</label>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="signature">Upload Signature</label>
                                            <div class="upload-card">
                                                <input class="form-control border-0" name="signature" id="signature" style="width: 90%;"
                                                       type="file">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button class="btn btn-primary w-100" type="submit" name="createAccount">Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->



    <!-- Footer Section Start  -->
    <footer id="copyright-sec">
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
