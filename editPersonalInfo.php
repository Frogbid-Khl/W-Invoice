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
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: 'Access Denied',
            text: 'You need to log in first.',
            icon: 'warning',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = "index.php";
        });
    </script>
    </body>
    </html>
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
    $logo = $_POST['inlogo'] ?? '';
    if (!empty($_FILES['logo']['name'])) {
        $RandomAccountNumber = mt_rand(1, 99999);
        $file_name = $RandomAccountNumber . "_" . basename($_FILES['logo']['name']);
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($file_tmp, "assets/logo/" . $file_name)) {
                $logo = "assets/logo/" . $file_name;
            }
        }
    }

    // Signature Upload
    $signature = $_POST['insignature'] ?? '';
    if (!empty($_FILES['signature']['name'])) {
        $RandomAccountNumber = mt_rand(1, 99999);
        $file_name = $RandomAccountNumber . "_" . basename($_FILES['signature']['name']);
        $file_tmp = $_FILES['signature']['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($file_tmp, "assets/signature/" . $file_name)) {
                $signature = "assets/signature/" . $file_name;
            }
        }
    }

    // Insert invoice data
    $update = $db_handle->insertQuery("
    UPDATE `user` SET 
        `name` = '$name',
        `address` = '$address',
        `logo` = '$logo',
        `pass` = '$pass',
        `signature` = '$signature',
        `toc` = '$terms',
        `status` = '1',
        `updated_at` = '$updated_at'
    WHERE `email` = '$email'
");

    if($update){
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
                text: 'Update Successful.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href="editPersonalInfo.php";
            });
        </script>
        </body>
        </html>
        <?php
    }
}

$userData='';

if(isset($_SESSION['uid'])){
    $query="SELECT * FROM `user` where uid='{$_SESSION['uid']}'";
    $userData=$db_handle->selectQuery($query);
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
                                                <input class="form-control" name="name" placeholder="" value="<?= $userData[0]['name'] ?>" type="text" required>
                                                <label>Name</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="address" value="<?= $userData[0]['address'] ?>" placeholder="" type="text">
                                                <label>Address</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="email" value="<?= $userData[0]['email'] ?>" placeholder="" type="text">
                                                <label>Email</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" name="pass" value="<?= $userData[0]['pass'] ?>" placeholder="" type="password">
                                                <label>Password</label>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="logo">Logo</label>
                                            <input type="hidden" name="inlogo" value="<?= $userData[0]['logo'] ?>"/>
                                            <div id="logoPreviewWrapper" class="<?= !empty($userData[0]['logo']) ? '' : 'd-none' ?>">
                                                <div class="position-relative d-inline-block">
                                                    <?php
                                                    $logo = $userData[0]['logo'];
                                                    $ext = pathinfo($logo, PATHINFO_EXTENSION);
                                                    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                                                    if (in_array(strtolower($ext), $imageExts)) {
                                                        // It's an image
                                                        echo '<img src="' . htmlspecialchars($logo) . '" alt="logo" class="img-fluid" id="logoPreview"/>';
                                                    } else {
                                                        // Not an image - show text
                                                        echo '<div style="font-size: 24px; font-weight: bold;">' . htmlspecialchars($logo) . '</div>';
                                                    }
                                                    ?>
                                                    <button type="button" class="btn-close position-absolute top-0 end-0" onclick="removeImage('logo')"></button>
                                                </div>
                                            </div>

                                            <div id="logoUpload" class="<?= empty($userData[0]['logo']) ? '' : 'd-none' ?>">
                                                <input class="form-control border-0" name="logo" id="logo" style="width: 90%;" type="file">
                                                <small>Or enter text instead:</small>
                                                <input class="form-control mt-2" id="logoText" name="logoText" placeholder="e.g. MyCompany" type="text">

                                                <!-- Display area -->
                                                <div id="logoDisplay" style="margin-top: 15px; min-height: 50px;">
                                                    <!-- Logo image or text will go here -->
                                                </div>

                                                <script>
                                                    const logoInput = document.getElementById('logo');
                                                    const logoTextInput = document.getElementById('logoText');
                                                    const logoDisplay = document.getElementById('logoDisplay');

                                                    // Watch for image upload
                                                    logoInput.addEventListener('change', function () {
                                                        const file = logoInput.files[0];
                                                        if (file) {
                                                            const reader = new FileReader();
                                                            reader.onload = function () {
                                                                logoDisplay.innerHTML = `<img src="${reader.result}" alt="Logo">`;
                                                            };
                                                            reader.readAsDataURL(file);
                                                        }
                                                    });

                                                    // Watch for text input (only if no image is selected)
                                                    logoTextInput.addEventListener('input', function () {
                                                        if (!logoInput.files[0]) {
                                                            const text = logoTextInput.value.trim();
                                                            logoDisplay.innerHTML = text
                                                                ? `<div style="font-size: 24px; font-weight: bold;">${text}</div>`
                                                                : '';
                                                        }
                                                    });

                                                </script>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mt-4">
                                        <div class="form-floating">
                                            <textarea class="form-control" name="terms" id="terms" placeholder="Leave a comment here"
                                                      style="height: 290px"><?= $userData[0]['toc'] ?></textarea>
                                            <label for="terms">Terms and Condition</label>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="logo">Signature</label>
                                            <input type="hidden" name="insign" value="<?= $userData[0]['signature'] ?>"/>
                                            <div id="signaturePreviewWrapper" class="<?= !empty($userData[0]['signature']) ? '' : 'd-none' ?>">
                                                <div class="position-relative d-inline-block">
                                                    <?php
                                                    $signature = $userData[0]['signature'];
                                                    $ext = pathinfo($signature, PATHINFO_EXTENSION);
                                                    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                                                    if (in_array(strtolower($ext), $imageExts)) {
                                                        // It's an image file
                                                        echo '<img src="' . htmlspecialchars($signature) . '" alt="Signature" class="img-fluid" id="signaturePreview">';
                                                    } else {
                                                        // It's text (not an image file)
                                                        echo '<div style="font-family: Pacifico, cursive; font-size: 22px; color: #444;">' . htmlspecialchars($signature) . '</div>';
                                                    }
                                                    ?>

                                                    <button type="button" class="btn-close position-absolute top-0 end-0" aria-label="Remove"
                                                            onclick="removeImage('signature')"></button>
                                                </div>
                                            </div>

                                            <div id="signatureUpload" class="<?= empty($userData[0]['signature']) ? '' : 'd-none' ?>">
                                                <input class="form-control border-0" name="signature" id="signature" style="width: 90%;" type="file">
                                                <small>Or type your name as signature:</small>
                                                <input class="form-control mt-2" id="signatureText" name="signatureText" placeholder="e.g. John Doe" type="text">
                                                <!-- Display area -->
                                                <div id="signatureDisplay" style="margin-top: 15px; min-height: 50px;">
                                                    <!-- Signature image or text will go here -->
                                                </div>

                                                <script>
                                                    const signatureInput = document.getElementById('signature');
                                                    const signatureTextInput = document.getElementById('signatureText');
                                                    const signatureDisplay = document.getElementById('signatureDisplay');

                                                    // Watch for image upload
                                                    signatureInput.addEventListener('change', function () {
                                                        const file = signatureInput.files[0];
                                                        if (file) {
                                                            const reader = new FileReader();
                                                            reader.onload = function () {
                                                                signatureDisplay.innerHTML = `<img src="${reader.result}" alt="Signature" style="max-height: 50px;">`;
                                                            };
                                                            reader.readAsDataURL(file);
                                                        }
                                                    });

                                                    // Watch for text input if no image
                                                    signatureTextInput.addEventListener('input', function () {
                                                        if (!signatureInput.files[0]) {
                                                            const text = signatureTextInput.value.trim();
                                                            signatureDisplay.innerHTML = text
                                                                ? `<div style="font-family: 'Pacifico', cursive; font-size: 20px; color: #333;">${text}</div>`
                                                                : '';
                                                        }
                                                    });
                                                </script>
                                            </div>


                                            <script>
                                                function removeImage(type) {
                                                    if (type === 'logo') {
                                                        document.getElementById('logoPreviewWrapper').classList.add('d-none');
                                                        document.getElementById('logoUpload').classList.remove('d-none');
                                                    }
                                                    if (type === 'signature') {
                                                        document.getElementById('signaturePreviewWrapper').classList.add('d-none');
                                                        document.getElementById('signatureUpload').classList.remove('d-none');
                                                    }
                                                }
                                            </script>
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
