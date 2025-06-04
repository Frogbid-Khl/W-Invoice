<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

if (!isset($_SESSION['uid'])) {
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

if (isset($_POST['saveInvoice'])) {
    $query = "SELECT * FROM `user` WHERE `uid`={$_SESSION['uid']}";

    $data = $db_handle->selectQuery($query);
    $credit = $data[0]['credit'];

    $uid=$_SESSION['uid'];

    if ($credit > 4) {

        $query="update `user` set  credit=credit-5 WHERE `uid`={$_SESSION['uid']}";
        $update=$db_handle->insertQuery($query);

        $from = $_POST['from'] ?? '';
        $billto = $_POST['billto'] ?? '';
        $shipto = $_POST['shipto'] ?? '';
        $invoice = $_POST['invoice'] ?? '';
        $invoiceDate = $_POST['invoiceDate'] ?? '';
        $currency = $_POST['currency'] ?? '';
        $dueDate = $_POST['dueDate'] ?? '';
        $terms = $_POST['terms'] ?? '';

        $invoiceOption = $_POST['invoiceOption'] ?? '1';

        $pname = $_POST['pname'] ?? [];
        $unitPrice = $_POST['unitPrice'] ?? [];
        $qty = $_POST['qty'] ?? [];
        $amount = $_POST['amount'] ?? [];
        $tax = $_POST['tax'] ?? '';

        $inserted_at = $updated_at = date("Y-m-d H:i:s");
        $sharable_url = generateRandomString(20);

        $history="-5 credit deduct from your credit for create ".$invoice;

        $query="INSERT INTO `credit_history`(`uid`, `history`, `inserted_at`) VALUES ('$uid','$history','$updated_at')";
        $update=$db_handle->insertQuery($query);


        // Example loop to insert line items if needed
        foreach ($pname as $key => $name) {
            $price = $unitPrice[$key];
            $quantity = $qty[$key];
            $line_amount = $amount[$key];
            $line_tax = $tax[$key];

            // You would need to get the invoice ID here if you want to link it properly
            $db_handle->insertQuery("INSERT INTO `invoice_detail`(`uid`, `isharable_url`, `pname`, `price`, `qty`, `amount`, `tax`, `inserted_at`, `updated_at`) VALUES ('$uid','$sharable_url','$name','$price','$quantity','$line_amount','$line_tax','$inserted_at','$updated_at')");
        }

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
        } else{
            $logo=$_POST['logoText'] ?? '';
        }

        // Signature Upload
        $signature = $_POST['insign'] ?? '';
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
        }else{
            $signature=$_POST['signatureText'] ?? '';
        }

        // Insert invoice data
        $insert = $db_handle->insertQuery("INSERT INTO `invoice`(`uid`, `ifrom`,`inv_view`, `invoiceDate`, `ibillto`, `ishipto`, `ilogo`, `iinv_no`, `icurrency`, 
        `idue_date`, `itoc`, `isignature`, `sharable_url`, `istatus`, `inserted_at`, `updated_at`) 
        VALUES ('$uid','$from','$invoiceOption','$invoiceDate','$billto','$shipto','$logo','$invoice','$currency','$dueDate','$terms','$signature',
        '$sharable_url','1','$inserted_at','$updated_at')");


        if ($insert) {
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
                    text: 'Invoice Added.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = "invoice" + "<?php echo $invoiceOption; ?>" + ".php?id=<?php echo $sharable_url; ?>";
                });
            </script>
            </body>
            </html>
            <?php
        }
    } else {
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
                text: 'Reload The Credit First for Updated.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = "createInvoice.php";
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
                <div class="hero-full-second pb-5 pt-5" id="create-invoice">
                    <div class="card p-4 bg-light">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="container py-5">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Design Your Custom Invoice</h2>
                                </div>

                                <div class="row g-4">
                                    <!-- Sender and Addresses -->
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label class="form-label" for="from">From <span style="color: red">*</span></label>
                                            <textarea class="form-control" name="from" id="from"
                                                      placeholder="Your Company or Name, Address, Contact Details"
                                                      rows="5" required><?= $userData[0]['name'] . "\n" . $userData[0]['address'] ?></textarea>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="billto">Bill To <span style="color: red">*</span></label>
                                            <textarea class="form-control" name="billto" id="billto"
                                                      placeholder="Customer Billing Address"
                                                      rows="5" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="shipto">Ship To <span style="color: red">*</span></label>
                                            <textarea class="form-control" name="shipto" id="shipto"
                                                      placeholder="Shipping Address (Optional)"
                                                      rows="5" required></textarea>
                                        </div>
                                    </div>

                                    <!-- Invoice Details -->
                                    <div class="col-lg-4">
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
                                                <input class="form-control mt-2" id="logoText" name="logoText" value="<?= $logo ?>" placeholder="e.g. MyCompany" type="text">

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
                                        <div class="mb-3">
                                            <label class="form-label" for="invoice">Invoice # <span style="color: red">*</span></label>
                                            <input class="form-control" id="invoice" name="invoice"
                                                   placeholder="e.g. INV-1001"
                                                   type="text" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="invoiceDate">Invoice Date <span style="color: red">*</span></label>
                                            <input class="form-control" name="invoiceDate" id="invoiceDate" type="date" required>

                                            <script>
                                                // Get today's date in YYYY-MM-DD format
                                                const today = new Date().toISOString().split('T')[0];
                                                // Set it as the value of the input field
                                                document.getElementById('invoiceDate').value = today;
                                            </script>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="currency">Currency <span style="color: red">*</span></label>
                                            <select class="form-select" name="currency" id="currency" onchange="changeCurrency(this.value);" required>
                                                <option value="BDT" <?php if($userData[0]['currency']=='BDT') echo 'selected'; ?>>BDT</option>
                                                <option value="USD" <?php if($userData[0]['currency']=='USD') echo 'selected'; ?>>USD</option>
                                            </select>

                                            <script>
                                                function changeCurrency(currency){
                                                    document.getElementById('currencyText').innerHTML=currency;
                                                }
                                            </script>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="dueDate">Due Date</label>
                                            <input class="form-control" name="dueDate" id="dueDate" type="date">

                                            <script>
                                                // Wait until the page content is fully loaded
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    // Create a new Date object for today
                                                    const today = new Date();

                                                    // Add 15 days
                                                    today.setDate(today.getDate() + 15);

                                                    // Format to YYYY-MM-DD
                                                    const dueDate = today.toISOString().split('T')[0];

                                                    // Set the value of the input field
                                                    document.getElementById('dueDate').value = dueDate;
                                                });
                                            </script>

                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <!-- Product Items Table -->
                                        <div class="mt-5">
                                            <h4 class="mb-3">Invoice Items <span style="color: red">*</span></h4>
                                            <div id="product-container">
                                                <div class="product-row mb-3 custom-grid">
                                                    <div class="form-floating">
                                                        <input class="form-control pname" name="pname[]"
                                                               placeholder="Product Name" type="text" required>
                                                        <label>Product Name</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control unitPrice" name="unitPrice[]"
                                                               placeholder="Unit Price" type="number" required>
                                                        <label>Unit Price</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control qty" name="qty[]"
                                                               placeholder="Quantity" type="number" required>
                                                        <label>Quantity</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control amount" name="amount[]"
                                                               placeholder="Amount" readonly type="number" required>
                                                        <label>Amount</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control tax" name="tax[]" placeholder="Tax"
                                                               type="text">
                                                        <label>Tax</label>
                                                    </div>
                                                    <div class="delete-wrapper d-flex align-items-center">
                                                        <button class="btn btn-danger delete-btn" type="button">X
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-outline-primary w-100 mt-3" id="add-product-btn"
                                                    type="button">
                                                + Add New Item
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="row">
                                            <div class="col-lg-6 ms-auto">
                                                <div class="p-4 rounded shadow-sm border bg-light">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0">Subtotal</h5>
                                                        <h5 class="mb-0" id="subtotal">0.0</h5>
                                                    </div>
                                                    <hr>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="mb-0 fw-bold">Total (<span id="currencyText">BDT</span>)</h4>
                                                        <h4 class="mb-0 fw-bold text-success" id="total">0.0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8 mt-4">
                                        <div class="form-floating">
                                            <textarea class="form-control" name="terms" id="terms"
                                                      placeholder="Leave a comment here"
                                                      style="height: 180px"><?= $userData[0]['toc'] ?></textarea>
                                            <label for="terms">Terms and Condition</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
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
                                                <input class="form-control mt-2" id="signatureText" name="signatureText" value="<?= $signature ?>" placeholder="e.g. John Doe" type="text">
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
                                    </div>
                                    <div class="col-lg-12">
                                        <!-- Selected Image Preview -->
                                        <div class="mb-3" id="selectedInvoicePreview" style="display: none;">
                                            <strong>Selected Invoice:</strong><br>
                                            <img id="selectedInvoiceImage" src="" alt="Selected Invoice"
                                                 style="max-width: 200px; border: 1px solid #ccc; border-radius: 8px;">
                                        </div>


                                        <button id="chooseOptionBtn" type="button" class="btn btn-primary mt-3"
                                                data-bs-toggle="modal" data-bs-target="#imageRadioModal">
                                            Choose an Invoice
                                        </button>

                                    </div>


                                    <!-- Modal -->
                                    <div class="modal fade" id="imageRadioModal" tabindex="-1"
                                         aria-labelledby="imageRadioModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageRadioModalLabel">Select an
                                                        Invoice</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <!-- Modal Body -->
                                                <div class="modal-body">
                                                    <div class="row" id="invoiceGrid"></div>
                                                </div>

                                                <!-- Include this script at the end of your modal or page -->
                                                <script>
                                                    // Generate 20 invoice options
                                                    const invoiceGrid = document.getElementById("invoiceGrid");
                                                    for (let i = 1; i <= 18; i++) {
                                                        const col = document.createElement("div");
                                                        col.className = "col-6 col-md-4 col-lg-4 mb-3";
                                                        col.innerHTML = `
                                                              <label class="image-radio w-100">
                                                                <input type="radio" name="invoiceOption" value="${i}">
                                                                <img src="assets/images/invoice${i}.png" alt="Invoice ${i}" class="img-fluid">
                                                                <div class="text-center">Invoice ${i}</div>
                                                              </label>
                                                            `;
                                                        invoiceGrid.appendChild(col);
                                                    }

                                                    // Add selection highlighting
                                                    document.addEventListener("change", function (e) {
                                                        if (e.target.name === "invoiceOption") {
                                                            document.querySelectorAll(".image-radio").forEach(label =>
                                                                label.classList.remove("selected")
                                                            );
                                                            e.target.closest(".image-radio").classList.add("selected");
                                                        }
                                                    });
                                                </script>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close
                                                    </button>
                                                    <button type="button" class="btn btn-primary"
                                                            onclick="submitSelection()">Submit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        const radios = document.querySelectorAll('.image-radio input[type="radio"]');

                                        radios.forEach(radio => {
                                            radio.addEventListener('change', () => {
                                                document.querySelectorAll('.image-radio').forEach(label => label.classList.remove('selected'));
                                                radio.closest('.image-radio').classList.add('selected');

                                                const selected = document.querySelector('input[name="invoiceOption"]:checked');
                                                if (selected) {
                                                    const selectedValue = selected.value;
                                                    const imageSrc = `assets/images/invoice${selectedValue}.png`;

                                                    // Show image preview
                                                    document.getElementById("selectedInvoiceImage").src = imageSrc;
                                                    document.getElementById("selectedInvoicePreview").style.display = "block";

                                                    // Change button text
                                                    document.getElementById("chooseOptionBtn").textContent = "Change the Invoice";

                                                    // Hide modal
                                                    const modal = bootstrap.Modal.getInstance(document.getElementById('imageRadioModal'));
                                                    modal.hide();
                                                } else {
                                                    alert("Please select an option.");
                                                }
                                            });
                                        });

                                        function submitSelection() {
                                            const selected = document.querySelector('input[name="invoiceOption"]:checked');
                                            if (selected) {
                                                alert("You selected option: " + selected.value);
                                                const modal = bootstrap.Modal.getInstance(document.getElementById('imageRadioModal'));
                                                modal.hide();
                                            } else {
                                                alert("Please select an option.");
                                            }
                                        }
                                    </script>
                                    <div class="col-12 mt-4">
                                        <button class="btn btn-primary w-100" type="submit" name="saveInvoice">Save
                                            Invoice, Print or Send via Email
                                        </button>
                                    </div>
                                </div>
                        </form>
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
<script src="assets/js/custom.js"></script>
</body>
</html>
