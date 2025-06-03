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

if (isset($_POST['saveInvoice'])) {

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


    // Example loop to insert line items if needed
    foreach ($pname as $key => $name) {
        $price = $unitPrice[$key];
        $quantity = $qty[$key];
        $line_amount = $amount[$key];
        $line_tax = $tax[$key];

        // You would need to get the invoice ID here if you want to link it properly
        $db_handle->insertQuery("INSERT INTO `invoice_detail`(`uid`, `isharable_url`, `pname`, `price`, `qty`, `amount`, `tax`, `inserted_at`, `updated_at`) VALUES ('0','$sharable_url','$name','$price','$quantity','$line_amount','$line_tax','$inserted_at','$updated_at')");
    }

    // Logo Upload
    $logo = '';
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
    $signature = '';
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

    $query="INSERT INTO `invoice`(`uid`, `ifrom`,`inv_view`,`invoiceDate`, `ibillto`, `ishipto`, `ilogo`, `iinv_no`, `icurrency`, 
        `idue_date`, `itoc`, `isignature`, `sharable_url`, `istatus`, `inserted_at`, `updated_at`) 
        VALUES ('0','$from','$invoiceOption','$invoiceDate','$billto','$shipto','$logo','$invoice','$currency','$dueDate','$terms','$signature',
        '$sharable_url','1','$inserted_at','$updated_at')";
    // Insert invoice data
    $insert=$db_handle->insertQuery($query);


    if($insert){

        $_SESSION['invoiceURL']=$sharable_url;
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
                window.location.href="invoice"+"<?php echo $invoiceOption; ?>"+".php?id=<?php echo $sharable_url; ?>";
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
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
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
                <div class="hero-full-first">
                    <div>
                        <h1 class="hero-txt1">All-in-one Invoice Template<br><span
                                class="restaurant-txt typed-text"></span><span class="cursor typing">&nbsp;</span></h1>
                        <p class="hero-txt2">All-in-one template comes with super clean code and easy to customize We
                            are not
                            using bootstrap! So your can use this Invoice template any kinds of project including
                            bootstrap template
                            of project </p>
                        <div class="hero-sec1">
                            <a class="demo-btn" href="#pages-sec">Demos</a>
                            <a class="buy-btn" href="#create-invoice">Create Invoice</a>
                        </div>
                    </div>
                </div>
                <div class="hero-full-second pb-5" id="create-invoice">
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
                                            <label class="form-label" for="from">From</label>
                                            <textarea class="form-control" name="from" id="from"
                                                      placeholder="Your Company or Name, Address"
                                                      rows="5"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="billto">Bill To</label>
                                            <textarea class="form-control" name="billto" id="billto"
                                                      placeholder="Customer Billing Address"
                                                      rows="5"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="shipto">Ship To</label>
                                            <textarea class="form-control" name="shipto" id="shipto"
                                                      placeholder="Shipping Address (Optional)"
                                                      rows="5"></textarea>
                                        </div>
                                    </div>

                                    <!-- Invoice Details -->
                                    <div class="col-lg-4">
                                        <div class="mb-4">
                                            <label class="form-label" for="logo">Upload Logo or Type Text</label>
                                            <div class="upload-card">
                                                <input class="form-control border-0" name="logo" id="logo" type="file" style="width: 90%;">
                                            </div>
                                            <small>Or enter text instead:</small>
                                            <input class="form-control mt-2" id="logoText" name="logoText" placeholder="e.g. MyCompany" type="text">
                                        </div>

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
                                        <div class="mb-3">
                                            <label class="form-label" for="invoice">Invoice #</label>
                                            <input class="form-control" id="invoice" name="invoice" placeholder="e.g. INV-1001"
                                                   type="text">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="invoiceDate">Invoice Date</label>
                                            <input class="form-control" name="invoiceDate" id="invoiceDate" type="date">

                                            <script>
                                                // Get today's date in YYYY-MM-DD format
                                                const today = new Date().toISOString().split('T')[0];
                                                // Set it as the value of the input field
                                                document.getElementById('invoiceDate').value = today;
                                            </script>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="currency">Currency</label>
                                            <select class="form-select" name="currency" id="currency" required>
                                                <option value="BDT">BDT</option>
                                                <option value="USD">USD</option>
                                            </select>
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
                                            <h4 class="mb-3">Invoice Items</h4>
                                            <div id="product-container">
                                                <div class="product-row mb-3 custom-grid">
                                                    <div class="form-floating">
                                                        <input class="form-control pname" name="pname[]" placeholder="Product Name" type="text">
                                                        <label>Product Name</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control unitPrice" name="unitPrice[]" placeholder="Unit Price" type="number">
                                                        <label>Unit Price</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control qty" name="qty[]" placeholder="Quantity" type="number">
                                                        <label>Quantity</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control amount" name="amount[]" placeholder="Amount" readonly type="number">
                                                        <label>Amount</label>
                                                    </div>
                                                    <div class="form-floating">
                                                        <input class="form-control tax" name="tax[]" placeholder="Tax" type="text">
                                                        <label>Tax</label>
                                                    </div>
                                                    <div class="delete-wrapper d-flex align-items-center">
                                                        <button class="btn btn-danger delete-btn" type="button">X</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-outline-primary w-100 mt-3" id="add-product-btn" type="button">
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
                                                        <h4 class="mb-0 fw-bold">Total (BDT)</h4>
                                                        <h4 class="mb-0 fw-bold text-success" id="total">0.0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8 mt-4">
                                        <div class="form-floating">
                                            <textarea class="form-control" name="terms" id="terms" placeholder="Leave a comment here"
                                                      style="height: 180px"></textarea>
                                            <label for="terms">Terms and Condition</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-4">
                                            <label class="form-label" for="signature">Upload Signature or Type Your Name</label>
                                            <div class="upload-card">
                                                <input class="form-control border-0" name="signature" id="signature" type="file" style="width: 90%;">
                                            </div>
                                            <small>Or type your name as signature:</small>
                                            <input class="form-control mt-2" id="signatureText" name="signatureText" placeholder="e.g. John Doe" type="text">
                                        </div>

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
                                    <div class="col-lg-12">
                                        <!-- Selected Image Preview -->
                                        <div class="mb-3" id="selectedInvoicePreview" style="display: none;">
                                            <strong>Selected Invoice:</strong><br>
                                            <img id="selectedInvoiceImage" src="" alt="Selected Invoice" style="max-width: 200px; border: 1px solid #ccc; border-radius: 8px;">
                                        </div>


                                        <button id="chooseOptionBtn" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#imageRadioModal">
                                            Choose an Invoice
                                        </button>

                                    </div>


                                    <!-- Modal -->
                                    <div class="modal fade" id="imageRadioModal" tabindex="-1" aria-labelledby="imageRadioModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageRadioModalLabel">Select an Invoice</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" onclick="submitSelection()">Submit</button>
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
                                        <button class="btn btn-primary w-100" type="submit" name="saveInvoice">Save Invoice, Print or Send via Email
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

    <!-- Invoice Section Start -->
    <section id="pages-sec">
        <div class="container">
            <div class="pages-top">
                <h2>15+ category based pre-made demo</h2>
            </div>
            <div class="full-pages-sec">
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice1.png">
                        </div>
                        <h3 class="invoice-page-txt">Agency Service Invoice </h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice1.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice2.png">
                        </div>
                        <h3 class="invoice-page-txt">Hotel Booking Invoice </h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice2.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice3.png">
                        </div>
                        <h3 class="invoice-page-txt">Restaurant Bill Invoice </h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice3.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice4.png">
                        </div>
                        <h3 class="invoice-page-txt">Bus Booking Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice4.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice5.png">
                        </div>
                        <h3 class="invoice-page-txt">Student Billing Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice5.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice6.png">
                        </div>
                        <h3 class="invoice-page-txt">Hospital or Medical Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice6.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice7.png">
                        </div>
                        <h3 class="invoice-page-txt">Movie Booking Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice7.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice8.png">
                        </div>
                        <h3 class="invoice-page-txt">eCommerce Bill Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice8.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice9.png">
                        </div>
                        <h3 class="invoice-page-txt">Flight Booking Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice9.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice10.png">
                        </div>
                        <h3 class="invoice-page-txt">Car Booking Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice10.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice11.png">
                        </div>
                        <h3 class="invoice-page-txt">Train Booking Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice11.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice12.png">
                        </div>
                        <h3 class="invoice-page-txt">Photostudio Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice12.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice13.png">
                        </div>
                        <h3 class="invoice-page-txt">Cleaning Service Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice13.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice14.png">
                        </div>
                        <h3 class="invoice-page-txt">Fitness Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice14.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice15.png">
                        </div>
                        <h3 class="invoice-page-txt">Travel Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice15.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice16.png">
                        </div>
                        <h3 class="invoice-page-txt">Coffee Shop Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice16.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice17.png">
                        </div>
                        <h3 class="invoice-page-txt">Internet Bill Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice17.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bottom_img_sec">
                    <div class="content">
                        <div class="light_img_sec">
                            <div class="content-overlay"></div>
                            <img alt="invoice-img" class="invoice-img" src="assets/images/invoice18.png">
                        </div>
                        <h3 class="invoice-page-txt">Domain & Hosting Invoice</h3>
                        <div class="content-details fadeIn-bottom">
                            <a class="link_under" href="invoice18.php" target="_blank">
                                <div class="button">
                                    <p class="button1">Use</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Invoice Section End -->

    <!-- Features Section Start -->
    <section id="features-sec">
        <div class="container">
            <div class="feature-top">
                <h2>Core Features</h2>
                <p>It has some unique features, like, no bootstrap only row code use,
                    can download PDF formate, clean & organize code, etc...</p>
            </div>
            <div class="features-sec-full">
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg1" fill="none" height="80" viewbox="0 0 63 63" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask fill="white" id="path-1-inside-1_310_38">
                                <path clip-rule="evenodd"
                                      d="M35.2614 0.217785C33.1577 0.625145 31.3336 1.97401 30.4211 3.79695C30.1562 4.32613 29.9038 5.02811 29.8602 5.35688L29.7809 5.9546H15.502H1.22301L0.925844 6.25189C0.532531 6.64508 0.532531 7.20163 0.925844 7.59482L1.22301 7.8921H15.5032H29.7834L29.924 8.45083C30.2821 9.87259 31.5679 11.646 32.2407 11.646C32.7455 11.646 32.9504 11.5325 33.153 11.1409C33.3785 10.7047 33.2953 10.4089 32.7649 9.76058C30.4097 6.88194 31.9765 2.80047 35.68 2.16618C36.3448 2.05223 36.5829 2.0687 37.4505 2.288C38.5446 2.5647 38.8604 2.52329 39.1847 2.06035C39.6396 1.41068 39.2265 0.755078 38.1676 0.446531C37.4816 0.246727 35.7971 0.114129 35.2614 0.217785ZM37.3546 4.07172C36.6889 4.27975 35.8894 4.75565 35.4776 5.18905C34.9123 5.7841 34.8832 6.57799 34.905 20.8491C34.9167 28.5563 34.8791 34.0137 34.8127 34.2272C34.703 34.5798 34.6863 34.5605 34.0514 33.3465C32.7412 30.8411 30.4044 28.9208 27.465 27.934C26.3891 27.5729 24.9608 27.5239 24.0959 27.8186C22.8563 28.2409 22.0293 29.3445 22.0118 30.5997C21.998 31.5908 22.4699 32.5223 23.8414 34.2112C26.5259 37.517 27.1704 38.6077 28.3602 41.8589C29.158 44.039 29.972 45.7438 30.9835 47.3539C32.5427 49.8357 33.4411 51.3714 34.3722 53.1463C35.2116 54.7467 35.4215 55.047 36.346 55.9712C37.9069 57.5316 40.9766 59.4774 41.5835 59.2913C41.9701 59.1728 42.1701 58.9926 42.3028 58.6433C42.5007 58.123 42.2221 57.7147 41.3329 57.2219C40.036 56.5034 38.4081 55.3511 37.6775 54.6343C37.2392 54.2043 36.7932 53.5821 36.419 52.8784C35.4104 50.982 34.2282 48.9422 33.0283 47.0282C31.5388 44.6523 31.1162 43.7966 30.1157 41.1323C28.8175 37.6746 28.0045 36.2466 25.8865 33.7037C24.0755 31.5294 23.7392 30.9233 23.9977 30.2992C24.4952 29.0982 27.0034 29.4413 29.3888 31.0369C30.7311 31.9347 31.6921 33.0348 32.5194 34.6205C32.9817 35.5067 33.4802 36.2536 33.9559 36.7727L34.6844 37.5677L34.5983 38.8296C34.5135 40.0729 34.5169 40.096 34.8208 40.4C35.2254 40.8047 35.7771 40.8093 36.175 40.4112C36.3385 40.2478 36.4723 39.989 36.4724 39.836C36.4724 39.6832 36.5258 38.5773 36.5911 37.3784C36.8956 31.7811 36.9395 29.3175 36.849 22.9077C36.7968 19.2113 36.7862 14.0162 36.8252 11.3628L36.8961 6.53864L37.1803 6.30856C37.3367 6.1819 37.6774 6.01454 37.9375 5.93668C38.5503 5.7531 38.9676 5.93184 39.1946 6.47531C39.7754 7.86522 40.4504 16.6147 41.0791 30.8999C41.2239 34.1923 41.2739 34.4306 41.8673 34.6562C42.2547 34.8035 42.6988 34.6315 42.9599 34.2331C43.1883 33.8846 43.2024 34.5021 42.7578 25.4639C42.5075 20.3776 42.5074 20.3277 42.7407 20.0147C43.1593 19.453 43.6946 19.229 44.6367 19.2214C45.7526 19.2123 46.2094 19.4126 46.6025 20.0833C46.9373 20.6547 47.0491 21.5929 47.7235 29.4966C47.9479 32.1253 47.9951 32.4181 48.2367 32.6753C48.672 33.1386 49.44 33.0427 49.7213 32.4898C49.9057 32.1272 49.876 31.6198 49.3162 25.5718C49.0657 22.8638 49.0108 21.8529 49.1111 21.7828C49.2883 21.6588 50.3085 21.778 51.0049 22.0041C51.6518 22.2141 52.1298 22.6278 52.4401 23.2457C53.3301 25.0189 54.0911 29.3543 54.006 32.1658C53.9723 33.2766 53.9862 33.3545 54.2562 33.5739C54.6548 33.8977 55.2568 33.8741 55.5879 33.5217C55.813 33.2821 55.8667 33.0482 55.9278 32.0383C55.9926 30.9681 55.8604 28.8756 55.6465 27.5866L55.569 27.1189L55.9806 27.2052C56.5882 27.3329 57.4392 27.7261 57.7648 28.0299C58.4415 28.661 58.7802 29.7802 59.055 32.2925C59.1569 33.2249 59.4839 35.7043 59.7815 37.8023C60.3986 42.1528 60.6915 44.9794 60.6901 46.5708C60.6881 48.6353 60.2684 51.4319 59.3888 55.2398C58.6013 58.6487 58.5593 58.1682 59.8176 60.1441C60.971 61.9549 61.3045 62.2722 61.8511 62.0776C62.3075 61.9151 62.6788 61.3875 62.5865 61.0325C62.546 60.8769 62.1261 60.1501 61.6536 59.4175C60.6626 57.8813 60.6879 58.2079 61.345 55.4214C61.5806 54.4224 61.9079 52.8149 62.0724 51.8491C62.8759 47.1316 62.8253 45.3235 61.6605 37.1362C61.3952 35.2714 61.0937 32.9697 60.9905 32.0212C60.6711 29.0855 60.2068 27.7573 59.1142 26.6533C58.2791 25.8094 56.8632 25.2187 55.658 25.2114L55.1659 25.2085L54.8775 24.2095C54.268 22.0982 53.3933 20.9007 52.015 20.2913C51.257 19.9559 49.7055 19.7032 49.1014 19.8166C48.7221 19.8878 48.6834 19.8637 48.5107 19.4503C48.2316 18.7824 47.3248 17.885 46.6095 17.5687C46.0836 17.3362 45.7759 17.2904 44.7672 17.295C43.949 17.2987 43.3852 17.365 43.0288 17.4997C42.7385 17.6094 42.4609 17.6741 42.4115 17.6435C42.3621 17.6131 42.2855 16.9732 42.2413 16.2215C42.0243 12.5325 41.5332 7.9099 41.1921 6.34622C40.7895 4.50111 39.1293 3.51698 37.3546 4.07172ZM43.5508 6.25189C43.3634 6.43922 43.2536 6.68746 43.2536 6.92335C43.2536 7.15924 43.3634 7.40749 43.5508 7.59482L43.848 7.8921H52.9411H62.0341L62.3313 7.59482C62.5187 7.40749 62.6285 7.15924 62.6285 6.92335C62.6285 6.68746 62.5187 6.43922 62.3313 6.25189L62.0341 5.9546H52.9411H43.848L43.5508 6.25189ZM5.86901 24.5357C3.46506 25.084 1.38527 27.1598 0.81468 29.5804C-0.235687 34.0366 3.40996 38.2663 7.96902 37.881C8.91403 37.8011 10.0947 37.4205 10.9468 36.9212C12.2526 36.1559 13.5204 34.4524 13.9101 32.9394L14.1236 32.1109H16.7664H19.4091L19.7063 31.8136C20.0996 31.4204 20.0996 30.8638 19.7063 30.4706L19.4091 30.1734H16.7664H14.1236L13.9101 29.3448C13.5192 27.8273 12.2674 26.1369 10.9721 25.3779C9.46828 24.4966 7.47968 24.1684 5.86901 24.5357ZM9.39926 26.7609C10.4919 27.2723 11.1988 27.9676 11.7407 29.0641C12.1681 29.9287 12.193 30.0432 12.193 31.1421C12.193 32.241 12.1681 32.3555 11.7407 33.2201C11.196 34.3223 10.4949 35.0088 9.3691 35.542C8.63758 35.8886 8.45012 35.9253 7.40981 35.9253C6.36949 35.9253 6.18204 35.8886 5.45051 35.542C4.3247 35.0088 3.62357 34.3223 3.07889 33.2201C2.65203 32.3564 2.62661 32.2401 2.62661 31.1484C2.62661 30.1009 2.66281 29.9153 3.00987 29.1828C3.49642 28.1557 4.1892 27.4038 5.0928 26.9222C6.04666 26.4139 6.56857 26.2988 7.68215 26.3519C8.40895 26.3865 8.79609 26.4787 9.39926 26.7609ZM21.0934 48.7156C18.4333 48.9974 15.9842 51.1652 15.3829 53.7702L15.2394 54.3921H8.23119H1.22301L0.925844 54.6894C0.532531 55.0826 0.532531 55.6391 0.925844 56.0323L1.22301 56.3296H8.23119H15.2394L15.3829 56.9515C15.8885 59.1421 17.7424 61.0829 20.0036 61.7888C20.5622 61.9631 21.1245 62.0345 21.9411 62.0345C22.7576 62.0345 23.32 61.9631 23.8786 61.7888C26.1397 61.0829 27.9936 59.1421 28.4993 56.9515L28.6427 56.3296H30.565C32.4444 56.3296 32.4939 56.323 32.7844 56.0323C32.9718 55.845 33.0817 55.5967 33.0817 55.3609C33.0817 55.125 32.9718 54.8767 32.7844 54.6894C32.4939 54.3988 32.4444 54.3921 30.565 54.3921H28.6427L28.4993 53.7702C28.2381 52.6387 27.6614 51.6314 26.7402 50.6975C25.1898 49.1255 23.343 48.4774 21.0934 48.7156ZM23.9305 50.9797C25.0231 51.4911 25.7301 52.1864 26.272 53.2829C26.6993 54.1475 26.7243 54.2619 26.7243 55.3609C26.7243 56.4598 26.6993 56.5742 26.272 57.4388C25.7273 58.541 25.0262 59.2275 23.9004 59.7608C23.1688 60.1074 22.9814 60.1441 21.9411 60.1441C20.9007 60.1441 20.7133 60.1074 19.9818 59.7608C18.856 59.2275 18.1548 58.541 17.6101 57.4388C17.1833 56.5752 17.1579 56.4588 17.1579 55.3672C17.1579 54.3197 17.1941 54.1341 17.5411 53.4016C18.0277 52.3744 18.7204 51.6226 19.624 51.141C20.5779 50.6326 21.0998 50.5176 22.2134 50.5706C22.9402 50.6053 23.3273 50.6974 23.9305 50.9797Z"
                                      fill-rule="evenodd"></path>
                            </mask>
                            <path clip-rule="evenodd"
                                  d="M35.2614 0.217785C33.1577 0.625145 31.3336 1.97401 30.4211 3.79695C30.1562 4.32613 29.9038 5.02811 29.8602 5.35688L29.7809 5.9546H15.502H1.22301L0.925844 6.25189C0.532531 6.64508 0.532531 7.20163 0.925844 7.59482L1.22301 7.8921H15.5032H29.7834L29.924 8.45083C30.2821 9.87259 31.5679 11.646 32.2407 11.646C32.7455 11.646 32.9504 11.5325 33.153 11.1409C33.3785 10.7047 33.2953 10.4089 32.7649 9.76058C30.4097 6.88194 31.9765 2.80047 35.68 2.16618C36.3448 2.05223 36.5829 2.0687 37.4505 2.288C38.5446 2.5647 38.8604 2.52329 39.1847 2.06035C39.6396 1.41068 39.2265 0.755078 38.1676 0.446531C37.4816 0.246727 35.7971 0.114129 35.2614 0.217785ZM37.3546 4.07172C36.6889 4.27975 35.8894 4.75565 35.4776 5.18905C34.9123 5.7841 34.8832 6.57799 34.905 20.8491C34.9167 28.5563 34.8791 34.0137 34.8127 34.2272C34.703 34.5798 34.6863 34.5605 34.0514 33.3465C32.7412 30.8411 30.4044 28.9208 27.465 27.934C26.3891 27.5729 24.9608 27.5239 24.0959 27.8186C22.8563 28.2409 22.0293 29.3445 22.0118 30.5997C21.998 31.5908 22.4699 32.5223 23.8414 34.2112C26.5259 37.517 27.1704 38.6077 28.3602 41.8589C29.158 44.039 29.972 45.7438 30.9835 47.3539C32.5427 49.8357 33.4411 51.3714 34.3722 53.1463C35.2116 54.7467 35.4215 55.047 36.346 55.9712C37.9069 57.5316 40.9766 59.4774 41.5835 59.2913C41.9701 59.1728 42.1701 58.9926 42.3028 58.6433C42.5007 58.123 42.2221 57.7147 41.3329 57.2219C40.036 56.5034 38.4081 55.3511 37.6775 54.6343C37.2392 54.2043 36.7932 53.5821 36.419 52.8784C35.4104 50.982 34.2282 48.9422 33.0283 47.0282C31.5388 44.6523 31.1162 43.7966 30.1157 41.1323C28.8175 37.6746 28.0045 36.2466 25.8865 33.7037C24.0755 31.5294 23.7392 30.9233 23.9977 30.2992C24.4952 29.0982 27.0034 29.4413 29.3888 31.0369C30.7311 31.9347 31.6921 33.0348 32.5194 34.6205C32.9817 35.5067 33.4802 36.2536 33.9559 36.7727L34.6844 37.5677L34.5983 38.8296C34.5135 40.0729 34.5169 40.096 34.8208 40.4C35.2254 40.8047 35.7771 40.8093 36.175 40.4112C36.3385 40.2478 36.4723 39.989 36.4724 39.836C36.4724 39.6832 36.5258 38.5773 36.5911 37.3784C36.8956 31.7811 36.9395 29.3175 36.849 22.9077C36.7968 19.2113 36.7862 14.0162 36.8252 11.3628L36.8961 6.53864L37.1803 6.30856C37.3367 6.1819 37.6774 6.01454 37.9375 5.93668C38.5503 5.7531 38.9676 5.93184 39.1946 6.47531C39.7754 7.86522 40.4504 16.6147 41.0791 30.8999C41.2239 34.1923 41.2739 34.4306 41.8673 34.6562C42.2547 34.8035 42.6988 34.6315 42.9599 34.2331C43.1883 33.8846 43.2024 34.5021 42.7578 25.4639C42.5075 20.3776 42.5074 20.3277 42.7407 20.0147C43.1593 19.453 43.6946 19.229 44.6367 19.2214C45.7526 19.2123 46.2094 19.4126 46.6025 20.0833C46.9373 20.6547 47.0491 21.5929 47.7235 29.4966C47.9479 32.1253 47.9951 32.4181 48.2367 32.6753C48.672 33.1386 49.44 33.0427 49.7213 32.4898C49.9057 32.1272 49.876 31.6198 49.3162 25.5718C49.0657 22.8638 49.0108 21.8529 49.1111 21.7828C49.2883 21.6588 50.3085 21.778 51.0049 22.0041C51.6518 22.2141 52.1298 22.6278 52.4401 23.2457C53.3301 25.0189 54.0911 29.3543 54.006 32.1658C53.9723 33.2766 53.9862 33.3545 54.2562 33.5739C54.6548 33.8977 55.2568 33.8741 55.5879 33.5217C55.813 33.2821 55.8667 33.0482 55.9278 32.0383C55.9926 30.9681 55.8604 28.8756 55.6465 27.5866L55.569 27.1189L55.9806 27.2052C56.5882 27.3329 57.4392 27.7261 57.7648 28.0299C58.4415 28.661 58.7802 29.7802 59.055 32.2925C59.1569 33.2249 59.4839 35.7043 59.7815 37.8023C60.3986 42.1528 60.6915 44.9794 60.6901 46.5708C60.6881 48.6353 60.2684 51.4319 59.3888 55.2398C58.6013 58.6487 58.5593 58.1682 59.8176 60.1441C60.971 61.9549 61.3045 62.2722 61.8511 62.0776C62.3075 61.9151 62.6788 61.3875 62.5865 61.0325C62.546 60.8769 62.1261 60.1501 61.6536 59.4175C60.6626 57.8813 60.6879 58.2079 61.345 55.4214C61.5806 54.4224 61.9079 52.8149 62.0724 51.8491C62.8759 47.1316 62.8253 45.3235 61.6605 37.1362C61.3952 35.2714 61.0937 32.9697 60.9905 32.0212C60.6711 29.0855 60.2068 27.7573 59.1142 26.6533C58.2791 25.8094 56.8632 25.2187 55.658 25.2114L55.1659 25.2085L54.8775 24.2095C54.268 22.0982 53.3933 20.9007 52.015 20.2913C51.257 19.9559 49.7055 19.7032 49.1014 19.8166C48.7221 19.8878 48.6834 19.8637 48.5107 19.4503C48.2316 18.7824 47.3248 17.885 46.6095 17.5687C46.0836 17.3362 45.7759 17.2904 44.7672 17.295C43.949 17.2987 43.3852 17.365 43.0288 17.4997C42.7385 17.6094 42.4609 17.6741 42.4115 17.6435C42.3621 17.6131 42.2855 16.9732 42.2413 16.2215C42.0243 12.5325 41.5332 7.9099 41.1921 6.34622C40.7895 4.50111 39.1293 3.51698 37.3546 4.07172ZM43.5508 6.25189C43.3634 6.43922 43.2536 6.68746 43.2536 6.92335C43.2536 7.15924 43.3634 7.40749 43.5508 7.59482L43.848 7.8921H52.9411H62.0341L62.3313 7.59482C62.5187 7.40749 62.6285 7.15924 62.6285 6.92335C62.6285 6.68746 62.5187 6.43922 62.3313 6.25189L62.0341 5.9546H52.9411H43.848L43.5508 6.25189ZM5.86901 24.5357C3.46506 25.084 1.38527 27.1598 0.81468 29.5804C-0.235687 34.0366 3.40996 38.2663 7.96902 37.881C8.91403 37.8011 10.0947 37.4205 10.9468 36.9212C12.2526 36.1559 13.5204 34.4524 13.9101 32.9394L14.1236 32.1109H16.7664H19.4091L19.7063 31.8136C20.0996 31.4204 20.0996 30.8638 19.7063 30.4706L19.4091 30.1734H16.7664H14.1236L13.9101 29.3448C13.5192 27.8273 12.2674 26.1369 10.9721 25.3779C9.46828 24.4966 7.47968 24.1684 5.86901 24.5357ZM9.39926 26.7609C10.4919 27.2723 11.1988 27.9676 11.7407 29.0641C12.1681 29.9287 12.193 30.0432 12.193 31.1421C12.193 32.241 12.1681 32.3555 11.7407 33.2201C11.196 34.3223 10.4949 35.0088 9.3691 35.542C8.63758 35.8886 8.45012 35.9253 7.40981 35.9253C6.36949 35.9253 6.18204 35.8886 5.45051 35.542C4.3247 35.0088 3.62357 34.3223 3.07889 33.2201C2.65203 32.3564 2.62661 32.2401 2.62661 31.1484C2.62661 30.1009 2.66281 29.9153 3.00987 29.1828C3.49642 28.1557 4.1892 27.4038 5.0928 26.9222C6.04666 26.4139 6.56857 26.2988 7.68215 26.3519C8.40895 26.3865 8.79609 26.4787 9.39926 26.7609ZM21.0934 48.7156C18.4333 48.9974 15.9842 51.1652 15.3829 53.7702L15.2394 54.3921H8.23119H1.22301L0.925844 54.6894C0.532531 55.0826 0.532531 55.6391 0.925844 56.0323L1.22301 56.3296H8.23119H15.2394L15.3829 56.9515C15.8885 59.1421 17.7424 61.0829 20.0036 61.7888C20.5622 61.9631 21.1245 62.0345 21.9411 62.0345C22.7576 62.0345 23.32 61.9631 23.8786 61.7888C26.1397 61.0829 27.9936 59.1421 28.4993 56.9515L28.6427 56.3296H30.565C32.4444 56.3296 32.4939 56.323 32.7844 56.0323C32.9718 55.845 33.0817 55.5967 33.0817 55.3609C33.0817 55.125 32.9718 54.8767 32.7844 54.6894C32.4939 54.3988 32.4444 54.3921 30.565 54.3921H28.6427L28.4993 53.7702C28.2381 52.6387 27.6614 51.6314 26.7402 50.6975C25.1898 49.1255 23.343 48.4774 21.0934 48.7156ZM23.9305 50.9797C25.0231 51.4911 25.7301 52.1864 26.272 53.2829C26.6993 54.1475 26.7243 54.2619 26.7243 55.3609C26.7243 56.4598 26.6993 56.5742 26.272 57.4388C25.7273 58.541 25.0262 59.2275 23.9004 59.7608C23.1688 60.1074 22.9814 60.1441 21.9411 60.1441C20.9007 60.1441 20.7133 60.1074 19.9818 59.7608C18.856 59.2275 18.1548 58.541 17.6101 57.4388C17.1833 56.5752 17.1579 56.4588 17.1579 55.3672C17.1579 54.3197 17.1941 54.1341 17.5411 53.4016C18.0277 52.3744 18.7204 51.6226 19.624 51.141C20.5779 50.6326 21.0998 50.5176 22.2134 50.5706C22.9402 50.6053 23.3273 50.6974 23.9305 50.9797Z"
                                  fill="#7E22CE"
                                  fill-rule="evenodd" mask="url(#path-1-inside-1_310_38)" stroke="#7E22CE"
                                  stroke-width="2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">Full Customization</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg2" fill="none" height="80" viewbox="0 0 63 63" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask fill="white" id="path-1-inside-1_310_44">
                                <path clip-rule="evenodd"
                                      d="M30.6066 0.660845L30.1949 1.07256V4.11807C30.1949 7.42587 30.241 7.6947 30.8575 7.97551C31.4397 8.24083 31.9862 8.10496 32.4022 7.59141C32.5878 7.36217 32.6117 6.98993 32.6141 4.28119C32.617 0.985135 32.5747 0.722119 31.9937 0.421685C31.4499 0.140506 31.0596 0.207834 30.6066 0.660845ZM10.8106 0.966365C10.5297 1.2473 10.4566 1.42725 10.4566 1.83824C10.4566 2.35071 10.483 2.38256 12.9754 4.87491L15.4941 7.39366H16.0468C16.6547 7.39366 17.1645 7.0242 17.2623 6.5127C17.3876 5.85698 17.1333 5.52022 14.656 3.06177C12.2283 0.652369 12.1796 0.612408 11.6763 0.612408C11.2722 0.612408 11.0902 0.686881 10.8106 0.966365ZM50.3449 0.960431C50.0851 1.15188 48.9053 2.28883 47.7232 3.48693C45.6489 5.58936 45.5738 5.6826 45.5738 6.15693C45.5738 6.50907 45.6627 6.75416 45.8873 7.02105C46.1543 7.33844 46.284 7.39366 46.762 7.39366H47.3232L49.8392 4.87781C52.3329 2.38401 52.355 2.35725 52.355 1.84115C52.355 1.42701 52.2825 1.24791 52.0011 0.966365C51.5288 0.494099 50.9803 0.492162 50.3449 0.960431ZM26.6032 10.3445C26.3262 10.4355 25.9356 10.6267 25.7356 10.7693C25.4054 11.0043 25.3136 11.0158 24.7445 10.8938C22.8497 10.4875 20.7177 11.6836 20.019 13.545C19.8132 14.0937 19.7682 14.4385 19.8007 15.2244L19.8414 16.2134L19.4117 16.342C18.7685 16.5348 17.8606 17.1813 17.4521 17.7379C16.763 18.6765 16.5878 19.2196 16.5837 20.4301L16.5798 21.5389L15.9405 21.8038C15.0735 22.1632 14.2144 22.9658 13.7818 23.8206C13.4598 24.4569 13.4236 24.6447 13.4235 25.6788C13.4234 26.6868 13.4632 26.9063 13.7448 27.4518L14.0662 28.0744L13.5631 28.2969C12.8097 28.6301 11.8602 29.6564 11.5371 30.4866C11.1334 31.5245 11.1545 32.7769 11.5919 33.734C11.7739 34.1323 12.0952 34.6364 12.3058 34.8543L12.6888 35.2504L10.6948 37.2519C8.96025 38.9928 8.66139 39.2447 8.39801 39.1873C7.01584 38.8866 6.78734 38.8685 6.1212 39.0059C5.00314 39.2366 4.54807 39.5478 2.87868 41.2237C0.922527 43.1873 0.714367 43.5609 0.710976 45.1144C0.707464 46.7143 0.939238 47.1161 3.10379 49.2618C4.79474 50.9381 4.85916 50.9874 5.35904 50.9874C6.05194 50.9874 6.5816 50.4871 6.5816 49.8324C6.5816 49.4211 6.44719 49.2481 4.89537 47.6623C2.48791 45.2023 2.48391 45.1129 4.68806 42.9044L6.16843 41.421H6.78794C7.32669 41.421 7.48387 41.4824 7.99355 41.8922C8.31591 42.1513 11.4551 45.2479 14.9698 48.7735C19.7881 53.6068 21.3886 55.2838 21.4769 55.5915C21.6991 56.3663 21.4769 56.7531 19.9346 58.2763C18.3738 59.8177 17.9921 60.0162 17.2055 59.6951C16.9902 59.6074 16.0707 58.8109 15.1623 57.9255C13.6479 56.4494 13.4735 56.3155 13.0641 56.3155C12.4066 56.3155 11.9097 56.8451 11.9097 57.5461C11.9097 58.0588 11.9438 58.1024 13.6959 59.8348C15.8471 61.9617 16.2067 62.1712 17.7244 62.1816C19.2697 62.1921 19.9078 61.8382 21.805 59.9183C23.6162 58.0855 23.953 57.4802 23.9444 56.0734C23.9405 55.4382 23.8601 54.9651 23.6766 54.4999L23.4145 53.8346L25.7442 51.5024C27.269 49.9763 28.2104 49.1254 28.4686 49.0401C28.7306 48.9536 29.8293 48.9332 31.7377 48.9791L34.6124 49.0482L37.0696 51.5055L39.5267 53.9626L39.3237 54.5034C39.205 54.8189 39.1159 55.4245 39.1094 55.9581C39.0922 57.3852 39.4856 58.0658 41.4214 59.9594C43.3257 61.8222 43.7493 62.0574 45.2105 62.0634C47.0962 62.071 46.5483 62.5129 54.5696 54.5142C62.6765 46.4298 62.3453 46.8354 62.3453 44.9933C62.3453 43.9847 62.3148 43.8471 61.936 43.1488C61.6441 42.6106 61.0857 41.9641 59.9889 40.8947C58.6944 39.6326 58.3376 39.3526 57.734 39.1259C56.8425 38.791 55.5462 38.766 54.7884 39.0692L54.2687 39.2772L52.264 37.2725L50.2593 35.2678L50.7162 34.6453C50.9674 34.3031 51.2802 33.6849 51.4114 33.2718C52.0286 31.326 51.037 29.1233 49.1433 28.2332C48.8466 28.0937 48.5178 27.9796 48.4127 27.9796C48.2719 27.9796 48.2434 27.9107 48.3047 27.7177C48.4573 27.2367 48.3769 25.8498 48.1646 25.3048C47.7049 24.1242 46.6427 23.0784 45.5796 22.76L45.15 22.6313V21.4608C45.15 20.4243 45.1112 20.2112 44.8116 19.6011C44.3924 18.7474 43.373 17.7904 42.5787 17.5047L41.9935 17.2944L41.9962 16.1888C41.9985 15.2456 41.9521 14.9831 41.6811 14.4026C40.9458 12.8279 39.7598 12.0638 37.7614 11.877C37.0964 11.8149 34.8358 11.4333 32.7379 11.0293C28.53 10.2187 27.3774 10.0899 26.6032 10.3445ZM31.2847 13.2253C32.7833 13.5048 34.0339 13.753 34.0639 13.7768C34.094 13.8005 33.416 14.5241 32.5573 15.3848L30.996 16.9494L30.5046 16.6823C29.7494 16.2718 28.0049 14.4609 27.6208 13.6888C27.4381 13.3216 27.2886 12.9189 27.2886 12.794C27.2886 12.5914 27.3568 12.575 27.9244 12.642C28.274 12.6833 29.7862 12.9457 31.2847 13.2253ZM24.7435 13.4473C24.8446 13.5014 25.022 13.8017 25.1379 14.1144C25.8282 15.9792 28.1364 18.3088 30.2376 19.2616C30.9414 19.5807 31.0442 19.6716 31.155 20.0728C31.4075 20.9872 30.7729 22.0705 29.8874 22.2365C28.8655 22.4283 28.7815 22.3686 25.5541 19.1608C23.9008 17.5175 22.4704 16.0258 22.3754 15.846C22.126 15.3739 22.1598 14.5496 22.4478 14.0771C22.8875 13.3558 24.002 13.0501 24.7435 13.4473ZM38.3911 14.4248C38.9081 14.6201 39.3876 15.1397 39.5262 15.6544C39.7518 16.4922 39.5474 16.8576 37.9491 18.4743C36.5236 19.9163 36.4918 19.9603 36.4918 20.4902C36.4918 21.2285 36.9445 21.6827 37.6804 21.6827C38.1487 21.6827 38.2658 21.6101 39.1344 20.7802C40.0985 19.8591 40.4952 19.6226 41.0709 19.6261C41.559 19.6291 42.3108 20.1148 42.5641 20.5909C43.0842 21.5684 42.741 22.3389 41.0799 23.923C40.5211 24.4558 39.969 25.0279 39.853 25.1944C39.308 25.9757 39.8767 27.0108 40.8506 27.0108C41.3132 27.0108 41.4326 26.9348 42.3492 26.0571C43.0068 25.4273 43.48 25.0737 43.7422 25.0161C44.5642 24.8356 45.3858 25.2064 45.7578 25.9257C45.9701 26.3362 45.9923 27.1615 45.8011 27.5335C45.7264 27.6789 45.0725 28.4026 44.348 29.1416C43.1054 30.4093 43.0308 30.5135 43.0308 30.9816C43.0308 31.6584 43.4106 32.0895 44.0864 32.1802C44.5824 32.2467 44.6198 32.2265 45.4183 31.4612C46.7032 30.2295 47.3299 30.0268 48.2517 30.5449C48.8918 30.9046 49.2604 31.6598 49.1421 32.369C49.0659 32.8261 48.8606 33.0825 47.315 34.6525C45.6592 36.3342 45.5738 36.4448 45.5738 36.9053C45.5738 37.6971 46.3171 38.2951 47.0672 38.1068C47.2227 38.0678 47.6088 37.8079 47.9253 37.5293L48.5007 37.0228L50.4579 38.9799L52.415 40.9369L46.8149 46.5371L41.2147 52.1372L39.2131 50.1355L37.2114 48.1338L38.3046 47.0406C39.4736 45.8717 39.5787 45.6333 39.2254 44.9503C39.026 44.5646 38.5772 44.3273 38.0473 44.3273C37.7528 44.3273 37.4986 44.5001 36.8074 45.1703C35.3867 46.548 34.681 46.7287 31.5184 46.5246C29.3968 46.3878 28.1884 46.4877 27.3115 46.8724C26.89 47.0572 26.0984 47.7654 24.2394 49.6202L21.735 52.1189L16.1564 46.5667C13.0881 43.5129 10.5777 40.9553 10.5777 40.8832C10.5777 40.8112 11.4452 39.8859 12.5054 38.8272L14.4332 36.9023L15.0181 37.1527C16.2342 37.6733 17.5386 37.6066 18.734 36.9627C19.8013 36.3877 20.6723 35.132 20.8318 33.9384C20.9162 33.3065 21.0192 33.1912 21.5018 33.1885C22.0947 33.1852 23.1486 32.6992 23.7649 32.1451C24.4733 31.5083 24.9812 30.552 25.0916 29.6473L25.1695 29.0089L25.8079 28.931C26.7126 28.8206 27.6689 28.3127 28.3057 27.6043C28.8598 26.988 29.3458 25.9341 29.349 25.3412C29.3518 24.8545 29.4671 24.7555 30.1343 24.6668C31.4746 24.4888 32.6932 23.5409 33.3204 22.1883C33.756 21.2492 33.7678 19.8678 33.3482 18.9182L33.063 18.2727L34.8077 16.5245C36.9233 14.4045 37.4777 14.0797 38.3911 14.4248ZM21.2098 18.6485C21.6385 18.7714 25.7679 22.6294 26.5175 23.6074C27.4794 24.8622 26.713 26.5265 25.1735 26.5265C24.9295 26.5265 24.6108 26.4662 24.4652 26.3925C24.3198 26.3188 23.0689 25.1337 21.6854 23.7588C18.9516 21.0418 18.7983 20.8169 19.0477 19.8908C19.1907 19.3594 19.7116 18.8113 20.2172 18.66C20.6924 18.5178 20.752 18.5172 21.2098 18.6485ZM18.0883 23.9823C18.6355 24.1343 22.5024 27.9802 22.6322 28.5015C22.7533 28.9876 22.6289 29.7269 22.3669 30.079C21.7856 30.8601 20.6372 31.0125 19.8293 30.416C19.1163 29.8896 16.2064 26.8838 15.9817 26.4417C15.5487 25.5898 15.9047 24.5524 16.7784 24.12C17.3662 23.829 17.4898 23.816 18.0883 23.9823ZM16.0939 30.4039C16.5505 30.5775 18.0838 32.0786 18.283 32.547C18.8754 33.9398 17.6297 35.3754 16.1723 34.9797C15.5537 34.8116 13.9176 33.1755 13.7495 32.5568C13.3698 31.1586 14.7485 29.8924 16.0939 30.4039ZM41.4294 40.635C40.7373 41.0231 40.6278 41.9429 41.2051 42.5202C42.0596 43.3747 43.5273 42.4979 43.1509 41.3577C43.0799 41.1423 42.9215 40.8752 42.799 40.7644C42.4814 40.477 41.8222 40.4147 41.4294 40.635ZM58.3658 42.7833C59.9127 44.3334 60.0719 44.5988 59.9278 45.3877C59.855 45.7864 59.0342 46.6531 53.1065 52.5907C49.4005 56.3032 46.1949 59.4228 45.983 59.5233C45.5201 59.743 45.0056 59.7519 44.6364 59.5468C44.4871 59.4639 43.7651 58.8031 43.0321 58.0782C41.6351 56.6969 41.3993 56.2969 41.5386 55.5445C41.5942 55.2441 42.9978 53.7702 48.2836 48.4618C51.9548 44.7748 55.122 41.6553 55.3218 41.5295C55.5511 41.3852 55.9065 41.3006 56.2853 41.3004L56.8854 41.2999L58.3658 42.7833ZM8.56608 52.4335C8.42549 52.4929 8.18864 52.6958 8.03993 52.8846C7.67932 53.3421 7.76081 54.0775 8.21564 54.4686C8.964 55.1124 10.2144 54.5703 10.2144 53.6022C10.2144 52.8001 9.27533 52.1343 8.56608 52.4335Z"
                                      fill-rule="evenodd"></path>
                            </mask>
                            <path clip-rule="evenodd"
                                  d="M30.6066 0.660845L30.1949 1.07256V4.11807C30.1949 7.42587 30.241 7.6947 30.8575 7.97551C31.4397 8.24083 31.9862 8.10496 32.4022 7.59141C32.5878 7.36217 32.6117 6.98993 32.6141 4.28119C32.617 0.985135 32.5747 0.722119 31.9937 0.421685C31.4499 0.140506 31.0596 0.207834 30.6066 0.660845ZM10.8106 0.966365C10.5297 1.2473 10.4566 1.42725 10.4566 1.83824C10.4566 2.35071 10.483 2.38256 12.9754 4.87491L15.4941 7.39366H16.0468C16.6547 7.39366 17.1645 7.0242 17.2623 6.5127C17.3876 5.85698 17.1333 5.52022 14.656 3.06177C12.2283 0.652369 12.1796 0.612408 11.6763 0.612408C11.2722 0.612408 11.0902 0.686881 10.8106 0.966365ZM50.3449 0.960431C50.0851 1.15188 48.9053 2.28883 47.7232 3.48693C45.6489 5.58936 45.5738 5.6826 45.5738 6.15693C45.5738 6.50907 45.6627 6.75416 45.8873 7.02105C46.1543 7.33844 46.284 7.39366 46.762 7.39366H47.3232L49.8392 4.87781C52.3329 2.38401 52.355 2.35725 52.355 1.84115C52.355 1.42701 52.2825 1.24791 52.0011 0.966365C51.5288 0.494099 50.9803 0.492162 50.3449 0.960431ZM26.6032 10.3445C26.3262 10.4355 25.9356 10.6267 25.7356 10.7693C25.4054 11.0043 25.3136 11.0158 24.7445 10.8938C22.8497 10.4875 20.7177 11.6836 20.019 13.545C19.8132 14.0937 19.7682 14.4385 19.8007 15.2244L19.8414 16.2134L19.4117 16.342C18.7685 16.5348 17.8606 17.1813 17.4521 17.7379C16.763 18.6765 16.5878 19.2196 16.5837 20.4301L16.5798 21.5389L15.9405 21.8038C15.0735 22.1632 14.2144 22.9658 13.7818 23.8206C13.4598 24.4569 13.4236 24.6447 13.4235 25.6788C13.4234 26.6868 13.4632 26.9063 13.7448 27.4518L14.0662 28.0744L13.5631 28.2969C12.8097 28.6301 11.8602 29.6564 11.5371 30.4866C11.1334 31.5245 11.1545 32.7769 11.5919 33.734C11.7739 34.1323 12.0952 34.6364 12.3058 34.8543L12.6888 35.2504L10.6948 37.2519C8.96025 38.9928 8.66139 39.2447 8.39801 39.1873C7.01584 38.8866 6.78734 38.8685 6.1212 39.0059C5.00314 39.2366 4.54807 39.5478 2.87868 41.2237C0.922527 43.1873 0.714367 43.5609 0.710976 45.1144C0.707464 46.7143 0.939238 47.1161 3.10379 49.2618C4.79474 50.9381 4.85916 50.9874 5.35904 50.9874C6.05194 50.9874 6.5816 50.4871 6.5816 49.8324C6.5816 49.4211 6.44719 49.2481 4.89537 47.6623C2.48791 45.2023 2.48391 45.1129 4.68806 42.9044L6.16843 41.421H6.78794C7.32669 41.421 7.48387 41.4824 7.99355 41.8922C8.31591 42.1513 11.4551 45.2479 14.9698 48.7735C19.7881 53.6068 21.3886 55.2838 21.4769 55.5915C21.6991 56.3663 21.4769 56.7531 19.9346 58.2763C18.3738 59.8177 17.9921 60.0162 17.2055 59.6951C16.9902 59.6074 16.0707 58.8109 15.1623 57.9255C13.6479 56.4494 13.4735 56.3155 13.0641 56.3155C12.4066 56.3155 11.9097 56.8451 11.9097 57.5461C11.9097 58.0588 11.9438 58.1024 13.6959 59.8348C15.8471 61.9617 16.2067 62.1712 17.7244 62.1816C19.2697 62.1921 19.9078 61.8382 21.805 59.9183C23.6162 58.0855 23.953 57.4802 23.9444 56.0734C23.9405 55.4382 23.8601 54.9651 23.6766 54.4999L23.4145 53.8346L25.7442 51.5024C27.269 49.9763 28.2104 49.1254 28.4686 49.0401C28.7306 48.9536 29.8293 48.9332 31.7377 48.9791L34.6124 49.0482L37.0696 51.5055L39.5267 53.9626L39.3237 54.5034C39.205 54.8189 39.1159 55.4245 39.1094 55.9581C39.0922 57.3852 39.4856 58.0658 41.4214 59.9594C43.3257 61.8222 43.7493 62.0574 45.2105 62.0634C47.0962 62.071 46.5483 62.5129 54.5696 54.5142C62.6765 46.4298 62.3453 46.8354 62.3453 44.9933C62.3453 43.9847 62.3148 43.8471 61.936 43.1488C61.6441 42.6106 61.0857 41.9641 59.9889 40.8947C58.6944 39.6326 58.3376 39.3526 57.734 39.1259C56.8425 38.791 55.5462 38.766 54.7884 39.0692L54.2687 39.2772L52.264 37.2725L50.2593 35.2678L50.7162 34.6453C50.9674 34.3031 51.2802 33.6849 51.4114 33.2718C52.0286 31.326 51.037 29.1233 49.1433 28.2332C48.8466 28.0937 48.5178 27.9796 48.4127 27.9796C48.2719 27.9796 48.2434 27.9107 48.3047 27.7177C48.4573 27.2367 48.3769 25.8498 48.1646 25.3048C47.7049 24.1242 46.6427 23.0784 45.5796 22.76L45.15 22.6313V21.4608C45.15 20.4243 45.1112 20.2112 44.8116 19.6011C44.3924 18.7474 43.373 17.7904 42.5787 17.5047L41.9935 17.2944L41.9962 16.1888C41.9985 15.2456 41.9521 14.9831 41.6811 14.4026C40.9458 12.8279 39.7598 12.0638 37.7614 11.877C37.0964 11.8149 34.8358 11.4333 32.7379 11.0293C28.53 10.2187 27.3774 10.0899 26.6032 10.3445ZM31.2847 13.2253C32.7833 13.5048 34.0339 13.753 34.0639 13.7768C34.094 13.8005 33.416 14.5241 32.5573 15.3848L30.996 16.9494L30.5046 16.6823C29.7494 16.2718 28.0049 14.4609 27.6208 13.6888C27.4381 13.3216 27.2886 12.9189 27.2886 12.794C27.2886 12.5914 27.3568 12.575 27.9244 12.642C28.274 12.6833 29.7862 12.9457 31.2847 13.2253ZM24.7435 13.4473C24.8446 13.5014 25.022 13.8017 25.1379 14.1144C25.8282 15.9792 28.1364 18.3088 30.2376 19.2616C30.9414 19.5807 31.0442 19.6716 31.155 20.0728C31.4075 20.9872 30.7729 22.0705 29.8874 22.2365C28.8655 22.4283 28.7815 22.3686 25.5541 19.1608C23.9008 17.5175 22.4704 16.0258 22.3754 15.846C22.126 15.3739 22.1598 14.5496 22.4478 14.0771C22.8875 13.3558 24.002 13.0501 24.7435 13.4473ZM38.3911 14.4248C38.9081 14.6201 39.3876 15.1397 39.5262 15.6544C39.7518 16.4922 39.5474 16.8576 37.9491 18.4743C36.5236 19.9163 36.4918 19.9603 36.4918 20.4902C36.4918 21.2285 36.9445 21.6827 37.6804 21.6827C38.1487 21.6827 38.2658 21.6101 39.1344 20.7802C40.0985 19.8591 40.4952 19.6226 41.0709 19.6261C41.559 19.6291 42.3108 20.1148 42.5641 20.5909C43.0842 21.5684 42.741 22.3389 41.0799 23.923C40.5211 24.4558 39.969 25.0279 39.853 25.1944C39.308 25.9757 39.8767 27.0108 40.8506 27.0108C41.3132 27.0108 41.4326 26.9348 42.3492 26.0571C43.0068 25.4273 43.48 25.0737 43.7422 25.0161C44.5642 24.8356 45.3858 25.2064 45.7578 25.9257C45.9701 26.3362 45.9923 27.1615 45.8011 27.5335C45.7264 27.6789 45.0725 28.4026 44.348 29.1416C43.1054 30.4093 43.0308 30.5135 43.0308 30.9816C43.0308 31.6584 43.4106 32.0895 44.0864 32.1802C44.5824 32.2467 44.6198 32.2265 45.4183 31.4612C46.7032 30.2295 47.3299 30.0268 48.2517 30.5449C48.8918 30.9046 49.2604 31.6598 49.1421 32.369C49.0659 32.8261 48.8606 33.0825 47.315 34.6525C45.6592 36.3342 45.5738 36.4448 45.5738 36.9053C45.5738 37.6971 46.3171 38.2951 47.0672 38.1068C47.2227 38.0678 47.6088 37.8079 47.9253 37.5293L48.5007 37.0228L50.4579 38.9799L52.415 40.9369L46.8149 46.5371L41.2147 52.1372L39.2131 50.1355L37.2114 48.1338L38.3046 47.0406C39.4736 45.8717 39.5787 45.6333 39.2254 44.9503C39.026 44.5646 38.5772 44.3273 38.0473 44.3273C37.7528 44.3273 37.4986 44.5001 36.8074 45.1703C35.3867 46.548 34.681 46.7287 31.5184 46.5246C29.3968 46.3878 28.1884 46.4877 27.3115 46.8724C26.89 47.0572 26.0984 47.7654 24.2394 49.6202L21.735 52.1189L16.1564 46.5667C13.0881 43.5129 10.5777 40.9553 10.5777 40.8832C10.5777 40.8112 11.4452 39.8859 12.5054 38.8272L14.4332 36.9023L15.0181 37.1527C16.2342 37.6733 17.5386 37.6066 18.734 36.9627C19.8013 36.3877 20.6723 35.132 20.8318 33.9384C20.9162 33.3065 21.0192 33.1912 21.5018 33.1885C22.0947 33.1852 23.1486 32.6992 23.7649 32.1451C24.4733 31.5083 24.9812 30.552 25.0916 29.6473L25.1695 29.0089L25.8079 28.931C26.7126 28.8206 27.6689 28.3127 28.3057 27.6043C28.8598 26.988 29.3458 25.9341 29.349 25.3412C29.3518 24.8545 29.4671 24.7555 30.1343 24.6668C31.4746 24.4888 32.6932 23.5409 33.3204 22.1883C33.756 21.2492 33.7678 19.8678 33.3482 18.9182L33.063 18.2727L34.8077 16.5245C36.9233 14.4045 37.4777 14.0797 38.3911 14.4248ZM21.2098 18.6485C21.6385 18.7714 25.7679 22.6294 26.5175 23.6074C27.4794 24.8622 26.713 26.5265 25.1735 26.5265C24.9295 26.5265 24.6108 26.4662 24.4652 26.3925C24.3198 26.3188 23.0689 25.1337 21.6854 23.7588C18.9516 21.0418 18.7983 20.8169 19.0477 19.8908C19.1907 19.3594 19.7116 18.8113 20.2172 18.66C20.6924 18.5178 20.752 18.5172 21.2098 18.6485ZM18.0883 23.9823C18.6355 24.1343 22.5024 27.9802 22.6322 28.5015C22.7533 28.9876 22.6289 29.7269 22.3669 30.079C21.7856 30.8601 20.6372 31.0125 19.8293 30.416C19.1163 29.8896 16.2064 26.8838 15.9817 26.4417C15.5487 25.5898 15.9047 24.5524 16.7784 24.12C17.3662 23.829 17.4898 23.816 18.0883 23.9823ZM16.0939 30.4039C16.5505 30.5775 18.0838 32.0786 18.283 32.547C18.8754 33.9398 17.6297 35.3754 16.1723 34.9797C15.5537 34.8116 13.9176 33.1755 13.7495 32.5568C13.3698 31.1586 14.7485 29.8924 16.0939 30.4039ZM41.4294 40.635C40.7373 41.0231 40.6278 41.9429 41.2051 42.5202C42.0596 43.3747 43.5273 42.4979 43.1509 41.3577C43.0799 41.1423 42.9215 40.8752 42.799 40.7644C42.4814 40.477 41.8222 40.4147 41.4294 40.635ZM58.3658 42.7833C59.9127 44.3334 60.0719 44.5988 59.9278 45.3877C59.855 45.7864 59.0342 46.6531 53.1065 52.5907C49.4005 56.3032 46.1949 59.4228 45.983 59.5233C45.5201 59.743 45.0056 59.7519 44.6364 59.5468C44.4871 59.4639 43.7651 58.8031 43.0321 58.0782C41.6351 56.6969 41.3993 56.2969 41.5386 55.5445C41.5942 55.2441 42.9978 53.7702 48.2836 48.4618C51.9548 44.7748 55.122 41.6553 55.3218 41.5295C55.5511 41.3852 55.9065 41.3006 56.2853 41.3004L56.8854 41.2999L58.3658 42.7833ZM8.56608 52.4335C8.42549 52.4929 8.18864 52.6958 8.03993 52.8846C7.67932 53.3421 7.76081 54.0775 8.21564 54.4686C8.964 55.1124 10.2144 54.5703 10.2144 53.6022C10.2144 52.8001 9.27533 52.1343 8.56608 52.4335Z"
                                  fill="#7E22CE"
                                  fill-rule="evenodd" mask="url(#path-1-inside-1_310_44)" stroke="#7E22CE"
                                  stroke-width="2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">User Friendly</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg3" fill="none" height="80" viewbox="0 0 63 55" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask fill="white" id="path-1-inside-1_310_46">
                                <path clip-rule="evenodd"
                                      d="M8.25201 1.04417C6.37651 1.4514 4.81464 3.03555 4.41116 4.93987C4.2696 5.60807 4.23618 7.25131 4.23448 13.652L4.23242 21.5405L3.29395 21.588C1.88817 21.659 1.05613 22.1605 0.44352 23.3058L0.175781 23.8064V38.0955V52.3845L0.439523 52.8771C0.584473 53.1481 0.92184 53.5674 1.18909 53.8088C2.15215 54.6789 0.719735 54.6247 22.776 54.6247C37.0544 54.6247 42.6903 54.5871 42.9029 54.4903C43.3683 54.2782 43.5088 53.8875 43.4379 53.0028C43.2793 51.0284 41.7645 49.2396 39.7273 48.6213C39.3469 48.5059 38.5376 48.4511 37.1981 48.4503L35.2283 48.449L35.2606 45.3308L35.293 42.2126H41.4688H47.6445L47.7051 47.3591L47.7656 52.5056L48.1093 53.0902C48.475 53.7124 49.2586 54.298 50.0016 54.5045C50.2456 54.5723 52.3338 54.6236 54.8578 54.624C59.8504 54.6249 60.1873 54.5804 61.0414 53.8088C61.3086 53.5674 61.646 53.1481 61.7909 52.8771L62.0547 52.3845V41.1833C62.0547 30.4915 62.0444 29.9629 61.8292 29.5583C61.2184 28.4103 60.3121 27.849 58.9365 27.7669L57.998 27.7108L57.996 16.7372C57.9942 7.6267 57.964 5.62296 57.8186 4.936C57.4094 3.00528 55.8504 1.44741 53.9173 1.03787C52.7199 0.784179 9.42214 0.790112 8.25201 1.04417ZM54.0108 3.17505C54.6626 3.47149 55.2847 4.07272 55.5982 4.70919L55.8789 5.27906L55.9122 16.5105L55.9455 27.7419H53.1183C50.9 27.7419 50.1743 27.7818 49.7482 27.927C48.457 28.3669 47.7051 29.4886 47.7051 30.9747V31.8591H36.3223H24.9395V28.1922C24.9395 25.3857 24.8985 24.3698 24.7648 23.8631L24.5903 23.2009L29.8508 17.9302C34.6364 13.1353 35.1113 12.6236 35.1113 12.2635C35.1113 11.7038 34.6543 11.2732 34.0605 11.2732C33.6204 11.2732 33.4146 11.4626 28.3258 16.5465C23.1768 21.6905 23.0394 21.8166 22.7141 21.6929C22.4941 21.6093 19.6456 21.5662 14.3359 21.5662H6.29102L6.29392 13.7253C6.29586 8.81862 6.34333 5.6806 6.42107 5.3396C6.66677 4.26054 7.5143 3.37159 8.57012 3.08581C9.01914 2.96423 13.7455 2.93722 31.3574 2.95539C51.5794 2.97622 53.617 2.99595 54.0108 3.17505ZM28.6068 5.15506C27.9948 5.4211 27.8713 6.23279 28.362 6.76245C28.6081 7.02801 28.6774 7.03491 31.1152 7.03491C33.5531 7.03491 33.6224 7.02801 33.8684 6.76245C34.3708 6.2202 34.2357 5.41674 33.5973 5.14997C33.0173 4.90766 29.1668 4.91154 28.6068 5.15506ZM34.539 16.6345C29.3654 21.808 29.1777 22.0118 29.1777 22.4527C29.1777 23.027 29.6381 23.5037 30.193 23.5037C30.525 23.5037 31.1789 22.892 35.9878 18.0833C40.9329 13.138 41.4082 12.6279 41.4082 12.2652C41.4082 11.7036 40.9522 11.2732 40.357 11.2732C39.9163 11.2732 39.7114 11.4621 34.539 16.6345ZM22.4805 23.917L22.8203 24.2093V36.2988V48.3884L12.4971 48.4194L2.17383 48.4503V36.3915V24.3327L2.52779 23.9787L2.88174 23.6248H12.5112H22.1407L22.4805 23.917ZM59.7027 30.1545L60.0566 30.5084V39.4787V48.449H54.9102H49.7637V39.4506C49.7637 29.9586 49.7471 30.2475 50.3086 29.9404C50.471 29.8515 52.0004 29.8067 54.9498 29.8042L59.3487 29.8005L59.7027 30.1545ZM11.1953 34.0255C10.5638 34.4218 6.43572 38.6784 6.35071 39.0208C6.25965 39.3876 6.53841 40.0231 6.84139 40.1394C6.94916 40.1807 7.20685 40.2146 7.41392 40.2146C7.74111 40.2146 8.09713 39.9094 10.1287 37.8877L12.4668 35.5606V34.8191C12.4668 34.1421 12.44 34.0655 12.1584 33.9372C11.7428 33.7478 11.6199 33.7591 11.1953 34.0255ZM17.4922 33.9709C17.0121 34.1886 12.7558 38.4851 12.5967 38.9128C12.3496 39.5768 12.814 40.2041 13.5577 40.2107C13.9509 40.2143 14.1654 40.0311 16.425 37.7624C17.7682 36.4138 18.8822 35.2013 18.9005 35.0681C18.9713 34.5543 18.7633 34.1474 18.3395 33.9703C17.8515 33.7663 17.9431 33.7663 17.4922 33.9709ZM47.6769 37.0359L47.7092 40.0935H36.3243H24.9395V37.005V33.9165L36.292 33.9474L47.6445 33.9783L47.6769 37.0359ZM30.763 36.1083C29.7549 36.5114 30.0603 38.0349 31.1491 38.0349C31.5622 38.0349 31.9662 37.7218 32.1214 37.2815C32.2567 36.8977 31.9568 36.3247 31.5266 36.1449C31.3046 36.0522 31.1076 35.9798 31.0888 35.9839C31.0701 35.9882 30.9234 36.0441 30.763 36.1083ZM56.1211 36.6755C55.9879 36.7018 55.1023 37.4945 54.1533 38.4371C52.5722 40.0074 52.4277 40.1873 52.4277 40.5855C52.4277 41.1717 52.8282 41.5466 53.4545 41.5466C53.8889 41.5466 54.0274 41.4415 55.4891 40.0027C56.3518 39.1536 57.1337 38.2817 57.2267 38.0652C57.5494 37.3143 56.9163 36.5186 56.1211 36.6755ZM26.9698 45.3308L27.0022 48.449H25.9708H24.9395V45.2957V42.1423L25.9385 42.1775L26.9375 42.2126L26.9698 45.3308ZM33.1738 45.3005V48.449H31.1152H29.0566V45.3005V42.1521H31.1152H33.1738V45.3005ZM22.8809 50.9958C22.8809 51.6898 22.6113 52.2558 22.1993 52.4264C21.938 52.5347 19.478 52.5624 12.3187 52.5381L2.78596 52.5056L2.51822 52.2379C2.31829 52.0379 2.23716 51.7849 2.19756 51.2388L2.14477 50.5076H12.5128H22.8809V50.9958ZM39.5918 50.7596C40.2613 51.0625 40.7509 51.5142 41.0585 52.112L41.2921 52.5662H33.0074H24.7228L24.8282 52.112C24.8862 51.8624 24.935 51.3992 24.9365 51.0828L24.9395 50.5076L31.9932 50.5102C38.8275 50.5128 39.0638 50.5205 39.5918 50.7596ZM60.0329 51.2388C59.9933 51.7847 59.9122 52.0379 59.7124 52.2379C59.448 52.5023 59.3935 52.5061 55.2399 52.542C52.9274 52.5622 50.8579 52.5459 50.6411 52.5062C50.0078 52.39 49.7637 52.0297 49.7637 51.2112V50.5076H54.9247H60.0857L60.0329 51.2388Z"
                                      fill-rule="evenodd"></path>
                            </mask>
                            <path clip-rule="evenodd"
                                  d="M8.25201 1.04417C6.37651 1.4514 4.81464 3.03555 4.41116 4.93987C4.2696 5.60807 4.23618 7.25131 4.23448 13.652L4.23242 21.5405L3.29395 21.588C1.88817 21.659 1.05613 22.1605 0.44352 23.3058L0.175781 23.8064V38.0955V52.3845L0.439523 52.8771C0.584473 53.1481 0.92184 53.5674 1.18909 53.8088C2.15215 54.6789 0.719735 54.6247 22.776 54.6247C37.0544 54.6247 42.6903 54.5871 42.9029 54.4903C43.3683 54.2782 43.5088 53.8875 43.4379 53.0028C43.2793 51.0284 41.7645 49.2396 39.7273 48.6213C39.3469 48.5059 38.5376 48.4511 37.1981 48.4503L35.2283 48.449L35.2606 45.3308L35.293 42.2126H41.4688H47.6445L47.7051 47.3591L47.7656 52.5056L48.1093 53.0902C48.475 53.7124 49.2586 54.298 50.0016 54.5045C50.2456 54.5723 52.3338 54.6236 54.8578 54.624C59.8504 54.6249 60.1873 54.5804 61.0414 53.8088C61.3086 53.5674 61.646 53.1481 61.7909 52.8771L62.0547 52.3845V41.1833C62.0547 30.4915 62.0444 29.9629 61.8292 29.5583C61.2184 28.4103 60.3121 27.849 58.9365 27.7669L57.998 27.7108L57.996 16.7372C57.9942 7.6267 57.964 5.62296 57.8186 4.936C57.4094 3.00528 55.8504 1.44741 53.9173 1.03787C52.7199 0.784179 9.42214 0.790112 8.25201 1.04417ZM54.0108 3.17505C54.6626 3.47149 55.2847 4.07272 55.5982 4.70919L55.8789 5.27906L55.9122 16.5105L55.9455 27.7419H53.1183C50.9 27.7419 50.1743 27.7818 49.7482 27.927C48.457 28.3669 47.7051 29.4886 47.7051 30.9747V31.8591H36.3223H24.9395V28.1922C24.9395 25.3857 24.8985 24.3698 24.7648 23.8631L24.5903 23.2009L29.8508 17.9302C34.6364 13.1353 35.1113 12.6236 35.1113 12.2635C35.1113 11.7038 34.6543 11.2732 34.0605 11.2732C33.6204 11.2732 33.4146 11.4626 28.3258 16.5465C23.1768 21.6905 23.0394 21.8166 22.7141 21.6929C22.4941 21.6093 19.6456 21.5662 14.3359 21.5662H6.29102L6.29392 13.7253C6.29586 8.81862 6.34333 5.6806 6.42107 5.3396C6.66677 4.26054 7.5143 3.37159 8.57012 3.08581C9.01914 2.96423 13.7455 2.93722 31.3574 2.95539C51.5794 2.97622 53.617 2.99595 54.0108 3.17505ZM28.6068 5.15506C27.9948 5.4211 27.8713 6.23279 28.362 6.76245C28.6081 7.02801 28.6774 7.03491 31.1152 7.03491C33.5531 7.03491 33.6224 7.02801 33.8684 6.76245C34.3708 6.2202 34.2357 5.41674 33.5973 5.14997C33.0173 4.90766 29.1668 4.91154 28.6068 5.15506ZM34.539 16.6345C29.3654 21.808 29.1777 22.0118 29.1777 22.4527C29.1777 23.027 29.6381 23.5037 30.193 23.5037C30.525 23.5037 31.1789 22.892 35.9878 18.0833C40.9329 13.138 41.4082 12.6279 41.4082 12.2652C41.4082 11.7036 40.9522 11.2732 40.357 11.2732C39.9163 11.2732 39.7114 11.4621 34.539 16.6345ZM22.4805 23.917L22.8203 24.2093V36.2988V48.3884L12.4971 48.4194L2.17383 48.4503V36.3915V24.3327L2.52779 23.9787L2.88174 23.6248H12.5112H22.1407L22.4805 23.917ZM59.7027 30.1545L60.0566 30.5084V39.4787V48.449H54.9102H49.7637V39.4506C49.7637 29.9586 49.7471 30.2475 50.3086 29.9404C50.471 29.8515 52.0004 29.8067 54.9498 29.8042L59.3487 29.8005L59.7027 30.1545ZM11.1953 34.0255C10.5638 34.4218 6.43572 38.6784 6.35071 39.0208C6.25965 39.3876 6.53841 40.0231 6.84139 40.1394C6.94916 40.1807 7.20685 40.2146 7.41392 40.2146C7.74111 40.2146 8.09713 39.9094 10.1287 37.8877L12.4668 35.5606V34.8191C12.4668 34.1421 12.44 34.0655 12.1584 33.9372C11.7428 33.7478 11.6199 33.7591 11.1953 34.0255ZM17.4922 33.9709C17.0121 34.1886 12.7558 38.4851 12.5967 38.9128C12.3496 39.5768 12.814 40.2041 13.5577 40.2107C13.9509 40.2143 14.1654 40.0311 16.425 37.7624C17.7682 36.4138 18.8822 35.2013 18.9005 35.0681C18.9713 34.5543 18.7633 34.1474 18.3395 33.9703C17.8515 33.7663 17.9431 33.7663 17.4922 33.9709ZM47.6769 37.0359L47.7092 40.0935H36.3243H24.9395V37.005V33.9165L36.292 33.9474L47.6445 33.9783L47.6769 37.0359ZM30.763 36.1083C29.7549 36.5114 30.0603 38.0349 31.1491 38.0349C31.5622 38.0349 31.9662 37.7218 32.1214 37.2815C32.2567 36.8977 31.9568 36.3247 31.5266 36.1449C31.3046 36.0522 31.1076 35.9798 31.0888 35.9839C31.0701 35.9882 30.9234 36.0441 30.763 36.1083ZM56.1211 36.6755C55.9879 36.7018 55.1023 37.4945 54.1533 38.4371C52.5722 40.0074 52.4277 40.1873 52.4277 40.5855C52.4277 41.1717 52.8282 41.5466 53.4545 41.5466C53.8889 41.5466 54.0274 41.4415 55.4891 40.0027C56.3518 39.1536 57.1337 38.2817 57.2267 38.0652C57.5494 37.3143 56.9163 36.5186 56.1211 36.6755ZM26.9698 45.3308L27.0022 48.449H25.9708H24.9395V45.2957V42.1423L25.9385 42.1775L26.9375 42.2126L26.9698 45.3308ZM33.1738 45.3005V48.449H31.1152H29.0566V45.3005V42.1521H31.1152H33.1738V45.3005ZM22.8809 50.9958C22.8809 51.6898 22.6113 52.2558 22.1993 52.4264C21.938 52.5347 19.478 52.5624 12.3187 52.5381L2.78596 52.5056L2.51822 52.2379C2.31829 52.0379 2.23716 51.7849 2.19756 51.2388L2.14477 50.5076H12.5128H22.8809V50.9958ZM39.5918 50.7596C40.2613 51.0625 40.7509 51.5142 41.0585 52.112L41.2921 52.5662H33.0074H24.7228L24.8282 52.112C24.8862 51.8624 24.935 51.3992 24.9365 51.0828L24.9395 50.5076L31.9932 50.5102C38.8275 50.5128 39.0638 50.5205 39.5918 50.7596ZM60.0329 51.2388C59.9933 51.7847 59.9122 52.0379 59.7124 52.2379C59.448 52.5023 59.3935 52.5061 55.2399 52.542C52.9274 52.5622 50.8579 52.5459 50.6411 52.5062C50.0078 52.39 49.7637 52.0297 49.7637 51.2112V50.5076H54.9247H60.0857L60.0329 51.2388Z"
                                  fill="#7E22CE"
                                  fill-rule="evenodd" mask="url(#path-1-inside-1_310_46)" stroke="#7E22CE"
                                  stroke-width="2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">User Friendly</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg4" fill="none" height="80" viewbox="0 0 80 80" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_36_473)">
                                <path d="M23.3333 26.6666L10 40L23.3333 53.3333" stroke="#7E22CE" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="3"></path>
                                <path d="M56.6666 26.6666L70 40L56.6666 53.3333" stroke="#7E22CE" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="3"></path>
                                <path d="M46.6667 13.3334L33.3334 66.6667" stroke="#7E22CE" stroke-linecap="round"
                                      stroke-linejoin="round" stroke-width="3"></path>
                            </g>
                            <defs>
                                <clippath id="clip0_36_473">
                                    <rect fill="white" height="80" width="80"></rect>
                                </clippath>
                            </defs>
                        </svg>
                    </div>
                    <h3 class="feature-txt">Developer Friendly Code</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg4" fill="none" height="80" viewbox="0 0 63 63" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.2684 1.42487L28.2684 1.42487C25.9462 1.64696 22.9619 2.32288 20.8293 3.10828L20.8293 3.10829C18.0507 4.13147 14.5952 6.09483 12.2589 7.97881C10.8361 9.12634 8.50613 11.5377 7.36267 13.0482L7.36266 13.0483L6.80592 13.7837L11.5251 18.5029L10.818 19.21L11.5251 18.5029L15.9017 22.8794C19.2935 16.5519 26.4743 12.9682 33.6286 14.0305L33.4817 15.0196L33.6286 14.0305C36.1478 14.4046 38.4102 15.2783 40.6072 16.7178C40.7448 16.808 40.8657 16.888 40.9732 16.9592C41.37 17.2221 41.5839 17.3637 41.7786 17.4433C41.8671 17.4794 41.9014 17.4787 41.9099 17.4785C41.9185 17.4783 41.9632 17.477 42.0688 17.4265C42.3227 17.3052 42.7164 17.0091 43.4264 16.3439C43.9838 15.8216 44.6764 15.1309 45.5859 14.224C45.8036 14.007 46.0336 13.7776 46.2771 13.5352L46.9826 14.244L46.2771 13.5352C47.3992 12.4185 48.4436 11.3547 49.2213 10.5415C49.6108 10.1343 49.9295 9.79396 50.1564 9.54311C50.2705 9.417 50.356 9.31942 50.4138 9.25023C50.4293 9.23162 50.441 9.21721 50.4495 9.20658C50.531 9.03923 50.5519 8.94144 50.5561 8.88831C50.5594 8.84703 50.5567 8.8004 50.5142 8.71546C50.3951 8.47707 50.0518 8.09781 49.1689 7.43259C44.5197 3.93007 39.41 1.95664 33.5907 1.41728M28.2684 1.42487L33.5907 1.41728M28.2684 1.42487C29.7211 1.28589 32.1354 1.28246 33.5907 1.41728M28.2684 1.42487L33.683 0.421544L33.5907 1.41728M45.3009 39.5635H45.8707V39.4821L45.4931 39.2688L45.3009 39.5635ZM45.3009 39.5635C45.361 39.4628 45.4198 39.3616 45.4772 39.2598L45.8707 38.5635M45.3009 39.5635L45.8707 38.5635M45.8707 38.5635L45 38.0716L44.7221 38.5635H45.8707ZM3.72598 19.0762L3.72603 19.076C3.73238 19.0616 3.73871 19.0473 3.74503 19.033C3.95753 19.2415 4.20063 19.4808 4.47007 19.7468C5.50747 20.771 6.92933 22.1848 8.48494 23.7405C8.48494 23.7406 8.48495 23.7406 8.48496 23.7406L13.8053 29.0619L13.7133 29.8853C13.7133 29.8853 13.7133 29.8853 13.7133 29.8853C13.5918 30.9721 13.5918 31.6237 13.7133 32.7105L13.8053 33.5339L8.48496 38.8553C8.48495 38.8553 8.48494 38.8553 8.48494 38.8553C6.92924 40.4112 5.50795 41.825 4.47149 42.8494C4.20401 43.1137 3.96254 43.3518 3.75123 43.5594C3.72193 43.4913 3.692 43.4212 3.66155 43.3493C3.3119 42.5238 2.91508 41.5134 2.65904 40.7659C2.09965 39.1327 1.64801 37.2571 1.34668 35.2999C1.21915 34.4711 1.14844 32.9136 1.14844 31.2979C1.14844 29.6822 1.21915 28.1246 1.34668 27.2959C1.79612 24.3763 2.57405 21.6912 3.72598 19.0762ZM43.6295 43.3418L43.8502 43.1072C44.8554 42.0386 45.6803 40.9335 46.3479 39.7518C46.3479 39.7517 46.3479 39.7517 46.3479 39.7516L46.7413 39.0555C46.9162 38.7459 46.9136 38.3667 46.7344 38.0595C46.5551 37.7524 46.2263 37.5635 45.8707 37.5635H38.3492C34.6894 37.5635 32.8265 37.5616 31.8165 37.5309C31.3056 37.5153 31.0636 37.4933 30.935 37.4716C30.8635 37.4595 30.8537 37.4545 30.8159 37.435C30.8123 37.4332 30.8084 37.4312 30.8042 37.429L30.8041 37.429C30.6843 37.3678 30.5798 37.2634 30.5187 37.1436L30.5187 37.1435C30.4885 37.0843 30.4837 37.0735 30.4728 37.0169C30.4541 36.9204 30.4328 36.7351 30.4174 36.3356C30.3867 35.5422 30.3841 34.108 30.3841 31.2979C30.3841 28.489 30.3867 27.0548 30.4174 26.2611C30.4329 25.8615 30.4541 25.6758 30.4728 25.5791C30.4838 25.5222 30.4886 25.5113 30.5185 25.4527L30.5185 25.4527C30.5779 25.3361 30.7061 25.2105 30.7921 25.1649C30.7945 25.1636 30.7966 25.1625 30.7983 25.1616C30.8168 25.1576 30.8628 25.149 30.9542 25.1393C31.2172 25.1117 31.703 25.0899 32.6765 25.0763C34.6052 25.0492 38.2947 25.0545 45.5698 25.0672C52.9429 25.0799 56.63 25.0866 58.5183 25.121C59.4757 25.1384 59.9224 25.1626 60.1439 25.1904C60.151 25.1913 60.1576 25.1922 60.1638 25.193C60.2625 25.2767 60.3787 25.3938 60.4357 25.4627C60.4425 25.4781 60.4593 25.5173 60.483 25.5898C60.5251 25.7187 60.5719 25.8994 60.6201 26.1327C60.7162 26.5979 60.8053 27.2104 60.8777 27.9106C61.0225 29.3107 61.0934 30.9892 61.0379 32.4092C60.7498 39.7768 57.9452 46.3837 52.7054 51.988L52.5053 52.202L48.0895 47.794L43.6295 43.3418ZM15.2743 40.3512C15.504 40.1227 15.713 39.915 15.8984 39.731C17.969 43.5546 21.7043 46.6212 25.9336 47.9648L25.9337 47.9648C27.7774 48.5505 28.903 48.7042 31.142 48.6997C33.961 48.6941 35.8472 48.283 38.3222 47.1257C38.3222 47.1257 38.3223 47.1257 38.3223 47.1257L38.8758 46.8669L43.5794 51.5695C45.0082 52.998 46.3059 54.3029 47.2456 55.2542C47.5239 55.5359 47.7702 55.7862 47.979 55.9991C47.8085 56.1121 47.6194 56.2349 47.418 56.3636C46.5788 56.8995 45.5709 57.5064 44.8711 57.8824L44.871 57.8825C42.8637 58.9612 39.7538 60.0994 37.4691 60.59C28.539 62.5077 19.5167 60.3815 12.3271 54.6605C10.8671 53.4987 8.54165 51.105 7.36279 49.5477L6.56547 50.1512L7.36278 49.5477L6.80623 48.8125L11.5104 44.1073C12.973 42.6444 14.3056 41.3148 15.2743 40.3512Z"
                                  stroke="#7E22CE" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">Google Font</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg6" fill="none" height="80" viewbox="0 0 54 63" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask fill="white" id="path-1-inside-1_310_72">
                                <path clip-rule="evenodd"
                                      d="M1.20189 1.14556L0.882812 1.39646V17.4139C0.882812 32.5168 0.894922 33.4419 1.09473 33.6173C1.63287 34.0898 2.35301 33.9414 2.57207 33.3129C2.65829 33.0658 2.69922 28.0743 2.69922 17.8296V2.71094H21.4688H40.2383V23.7207V44.7305L36.6357 44.7343C32.6794 44.7386 32.6951 44.7357 32.4837 45.4729C32.4196 45.6964 32.3672 47.3645 32.3672 49.1798V52.4805H17.5332H2.69922V44.5428C2.69922 37.8131 2.67221 36.553 2.52206 36.2626C2.22695 35.692 1.57668 35.5914 1.14607 36.0497L0.882812 36.3301V45.0502V53.7702L1.16302 54.0336C1.44202 54.2957 1.46866 54.2969 6.73334 54.2969H12.0234V58.1035C12.0234 61.9547 12.0691 62.4156 12.4823 62.7295C12.6497 62.8566 15.773 62.8875 28.7908 62.8907L44.8923 62.8945L49.0437 58.7494L53.1953 54.6043V38.2452V21.886L52.9151 21.6226C52.5691 21.2977 52.1028 21.2853 51.7254 21.5909L51.4395 21.8223L51.4078 37.5753L51.3762 53.3281H47.7997H44.2234L43.9262 53.6254L43.6289 53.9226V57.5004V61.0781H28.7344H13.8398V57.6875V54.2969H23.7956H33.7513L37.7299 50.3311C39.9182 48.1498 41.7866 46.2181 41.8816 46.0383C42.0293 45.7593 42.0547 43.1849 42.0547 28.51V11.3086H46.712H51.3691L51.4043 15.1337L51.4395 18.9587L51.7895 19.2597C52.171 19.5877 52.5849 19.5712 52.9834 19.212C53.178 19.0367 53.1953 18.6533 53.1953 14.5199V10.0188L52.9151 9.75545C52.6361 9.4934 52.6095 9.49219 47.3448 9.49219H42.0547V5.68561C42.0547 1.83434 42.009 1.37346 41.5959 1.05958C41.4279 0.93207 37.6167 0.901555 21.4548 0.898406L1.52098 0.894531L1.20189 1.14556ZM18.8755 7.80983C17.2741 8.0433 15.6515 8.61389 14.1426 9.47414C13.0994 10.0688 12.75 10.4263 12.75 10.8987C12.75 11.3137 13.197 11.793 13.584 11.793C13.6917 11.793 14.243 11.5203 14.8088 11.1869C17.8707 9.38357 21.3741 9.01689 24.6403 10.1581C27.5984 11.1916 30.2941 13.7466 31.4639 16.6257C32.474 19.1115 32.5781 22.2201 31.7341 24.6895C30.5101 28.2701 27.5362 31.0957 23.8906 32.1417C22.7538 32.4679 20.2421 32.5923 18.9264 32.3876C12.2521 31.3492 7.8876 24.474 9.71975 17.8846C10.0512 16.6925 10.4148 15.9064 11.224 14.6325C11.9647 13.4661 12.0142 13.1492 11.5205 12.7314C10.9486 12.2476 10.4508 12.5161 9.6144 13.7597C7.39802 17.0545 6.86714 21.7338 8.27474 25.567C9.90127 29.9966 13.7378 33.2583 18.3506 34.1333C19.59 34.3685 22.0155 34.3685 23.2549 34.1333C32.1025 32.455 36.787 22.6943 32.5591 14.7467C29.9466 9.83597 24.3694 7.00855 18.8755 7.80983ZM25.2227 15.3747C24.9707 15.4826 23.6354 16.7174 21.9284 18.421L19.0581 21.2857L17.8668 20.1298C16.8409 19.1343 16.5909 18.9535 16.0658 18.8275C14.4898 18.4494 12.9866 19.6593 12.9956 21.2988C12.9997 22.0543 13.4528 22.6961 15.3121 24.5799C17.4682 26.7643 17.864 27.0658 18.679 27.144C19.9995 27.2709 19.8281 27.3978 24.1768 23.075C26.7978 20.4696 28.2334 18.9537 28.3822 18.6348C28.708 17.9359 28.6746 17.2662 28.2762 16.5034C27.7002 15.401 26.3373 14.8971 25.2227 15.3747ZM26.5754 17.2004C26.6972 17.3106 26.7969 17.513 26.7969 17.65C26.7969 17.8152 25.5431 19.1553 23.0759 21.6274C20.1694 24.5393 19.2883 25.3555 19.0509 25.3555C18.8196 25.3555 18.2759 24.8851 16.7777 23.389C15.6946 22.3075 14.8086 21.3371 14.8086 21.2325C14.8086 20.9012 15.1186 20.6025 15.4626 20.6025C15.7242 20.6025 16.0982 20.9021 17.2202 22.0103C18.4712 23.2459 18.6953 23.418 19.0534 23.418C19.4255 23.418 19.744 23.1358 22.6751 20.2096C24.6404 18.2474 25.979 17.001 26.1213 17.0006C26.2493 17.0002 26.4536 17.0902 26.5754 17.2004ZM10.0212 37.4581C9.43276 37.6988 9.2453 38.4573 9.66066 38.9164C9.87887 39.1575 10.0099 39.1602 21.6504 39.1602C33.2909 39.1602 33.4219 39.1575 33.6401 38.9164C33.9425 38.5823 33.9212 37.9963 33.595 37.67L33.3293 37.4043L21.7984 37.3808C15.4564 37.3678 10.1567 37.4026 10.0212 37.4581ZM9.71587 41.9264C9.35175 42.2213 9.3469 42.8343 9.70582 43.1933L9.97163 43.459H18.1293C25.9816 43.459 26.2966 43.4504 26.542 43.2283C26.8746 42.9273 26.881 42.2717 26.5547 41.9453C26.3173 41.708 26.1511 41.7031 18.1521 41.7031C10.402 41.7031 9.9778 41.7144 9.71587 41.9264ZM38.9062 46.5784C38.9062 46.5957 37.8709 47.6441 36.6055 48.9082L34.3047 51.2066V48.8767V46.5469H36.6055C37.8709 46.5469 38.9062 46.561 38.9062 46.5784ZM50.0469 55.176C50.0469 55.1933 49.0115 56.2418 47.7461 57.5059L45.4453 59.8042V57.4744V55.1445H47.7461C49.0115 55.1445 50.0469 55.1587 50.0469 55.176Z"
                                      fill-rule="evenodd"></path>
                            </mask>
                            <path clip-rule="evenodd"
                                  d="M1.20189 1.14556L0.882812 1.39646V17.4139C0.882812 32.5168 0.894922 33.4419 1.09473 33.6173C1.63287 34.0898 2.35301 33.9414 2.57207 33.3129C2.65829 33.0658 2.69922 28.0743 2.69922 17.8296V2.71094H21.4688H40.2383V23.7207V44.7305L36.6357 44.7343C32.6794 44.7386 32.6951 44.7357 32.4837 45.4729C32.4196 45.6964 32.3672 47.3645 32.3672 49.1798V52.4805H17.5332H2.69922V44.5428C2.69922 37.8131 2.67221 36.553 2.52206 36.2626C2.22695 35.692 1.57668 35.5914 1.14607 36.0497L0.882812 36.3301V45.0502V53.7702L1.16302 54.0336C1.44202 54.2957 1.46866 54.2969 6.73334 54.2969H12.0234V58.1035C12.0234 61.9547 12.0691 62.4156 12.4823 62.7295C12.6497 62.8566 15.773 62.8875 28.7908 62.8907L44.8923 62.8945L49.0437 58.7494L53.1953 54.6043V38.2452V21.886L52.9151 21.6226C52.5691 21.2977 52.1028 21.2853 51.7254 21.5909L51.4395 21.8223L51.4078 37.5753L51.3762 53.3281H47.7997H44.2234L43.9262 53.6254L43.6289 53.9226V57.5004V61.0781H28.7344H13.8398V57.6875V54.2969H23.7956H33.7513L37.7299 50.3311C39.9182 48.1498 41.7866 46.2181 41.8816 46.0383C42.0293 45.7593 42.0547 43.1849 42.0547 28.51V11.3086H46.712H51.3691L51.4043 15.1337L51.4395 18.9587L51.7895 19.2597C52.171 19.5877 52.5849 19.5712 52.9834 19.212C53.178 19.0367 53.1953 18.6533 53.1953 14.5199V10.0188L52.9151 9.75545C52.6361 9.4934 52.6095 9.49219 47.3448 9.49219H42.0547V5.68561C42.0547 1.83434 42.009 1.37346 41.5959 1.05958C41.4279 0.93207 37.6167 0.901555 21.4548 0.898406L1.52098 0.894531L1.20189 1.14556ZM18.8755 7.80983C17.2741 8.0433 15.6515 8.61389 14.1426 9.47414C13.0994 10.0688 12.75 10.4263 12.75 10.8987C12.75 11.3137 13.197 11.793 13.584 11.793C13.6917 11.793 14.243 11.5203 14.8088 11.1869C17.8707 9.38357 21.3741 9.01689 24.6403 10.1581C27.5984 11.1916 30.2941 13.7466 31.4639 16.6257C32.474 19.1115 32.5781 22.2201 31.7341 24.6895C30.5101 28.2701 27.5362 31.0957 23.8906 32.1417C22.7538 32.4679 20.2421 32.5923 18.9264 32.3876C12.2521 31.3492 7.8876 24.474 9.71975 17.8846C10.0512 16.6925 10.4148 15.9064 11.224 14.6325C11.9647 13.4661 12.0142 13.1492 11.5205 12.7314C10.9486 12.2476 10.4508 12.5161 9.6144 13.7597C7.39802 17.0545 6.86714 21.7338 8.27474 25.567C9.90127 29.9966 13.7378 33.2583 18.3506 34.1333C19.59 34.3685 22.0155 34.3685 23.2549 34.1333C32.1025 32.455 36.787 22.6943 32.5591 14.7467C29.9466 9.83597 24.3694 7.00855 18.8755 7.80983ZM25.2227 15.3747C24.9707 15.4826 23.6354 16.7174 21.9284 18.421L19.0581 21.2857L17.8668 20.1298C16.8409 19.1343 16.5909 18.9535 16.0658 18.8275C14.4898 18.4494 12.9866 19.6593 12.9956 21.2988C12.9997 22.0543 13.4528 22.6961 15.3121 24.5799C17.4682 26.7643 17.864 27.0658 18.679 27.144C19.9995 27.2709 19.8281 27.3978 24.1768 23.075C26.7978 20.4696 28.2334 18.9537 28.3822 18.6348C28.708 17.9359 28.6746 17.2662 28.2762 16.5034C27.7002 15.401 26.3373 14.8971 25.2227 15.3747ZM26.5754 17.2004C26.6972 17.3106 26.7969 17.513 26.7969 17.65C26.7969 17.8152 25.5431 19.1553 23.0759 21.6274C20.1694 24.5393 19.2883 25.3555 19.0509 25.3555C18.8196 25.3555 18.2759 24.8851 16.7777 23.389C15.6946 22.3075 14.8086 21.3371 14.8086 21.2325C14.8086 20.9012 15.1186 20.6025 15.4626 20.6025C15.7242 20.6025 16.0982 20.9021 17.2202 22.0103C18.4712 23.2459 18.6953 23.418 19.0534 23.418C19.4255 23.418 19.744 23.1358 22.6751 20.2096C24.6404 18.2474 25.979 17.001 26.1213 17.0006C26.2493 17.0002 26.4536 17.0902 26.5754 17.2004ZM10.0212 37.4581C9.43276 37.6988 9.2453 38.4573 9.66066 38.9164C9.87887 39.1575 10.0099 39.1602 21.6504 39.1602C33.2909 39.1602 33.4219 39.1575 33.6401 38.9164C33.9425 38.5823 33.9212 37.9963 33.595 37.67L33.3293 37.4043L21.7984 37.3808C15.4564 37.3678 10.1567 37.4026 10.0212 37.4581ZM9.71587 41.9264C9.35175 42.2213 9.3469 42.8343 9.70582 43.1933L9.97163 43.459H18.1293C25.9816 43.459 26.2966 43.4504 26.542 43.2283C26.8746 42.9273 26.881 42.2717 26.5547 41.9453C26.3173 41.708 26.1511 41.7031 18.1521 41.7031C10.402 41.7031 9.9778 41.7144 9.71587 41.9264ZM38.9062 46.5784C38.9062 46.5957 37.8709 47.6441 36.6055 48.9082L34.3047 51.2066V48.8767V46.5469H36.6055C37.8709 46.5469 38.9062 46.561 38.9062 46.5784ZM50.0469 55.176C50.0469 55.1933 49.0115 56.2418 47.7461 57.5059L45.4453 59.8042V57.4744V55.1445H47.7461C49.0115 55.1445 50.0469 55.1587 50.0469 55.176Z"
                                  fill="#7E22CE"
                                  fill-rule="evenodd" mask="url(#path-1-inside-1_310_72)" stroke="#7E22CE"
                                  stroke-width="2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">Well Documentation</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg7" fill="none" height="80" viewbox="0 0 63 63" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd"
                                  d="M3.39127 0.586911C2.56735 0.840602 1.70492 1.46726 1.25651 2.13824C0.440334 3.35947 0.495431 1.12311 0.529943 31.6438L0.561306 59.3095L0.876271 59.9811C1.32589 60.9395 1.85761 61.4871 2.77914 61.9407L3.58356 62.3368H31.5008H59.418L60.2224 61.9407C61.1439 61.4871 61.6756 60.9395 62.1252 59.9811L62.4402 59.3095L62.4716 31.6438C62.5058 1.2947 62.5549 3.40028 61.7853 2.18934C61.5864 1.87608 61.1618 1.42997 60.842 1.19783C59.6538 0.335399 61.9237 0.396431 31.4322 0.405876C8.003 0.41302 3.86922 0.439661 3.39127 0.586911ZM59.3803 3.51786L59.7761 3.91372V31.3974V58.8811L59.3803 59.2769L58.9844 59.6728H31.5008H4.01708L3.62122 59.2769L3.22537 58.8811V31.4048V3.92861L3.5281 3.60287C3.69461 3.42377 3.93982 3.23184 4.07302 3.17662C4.20623 3.12128 16.6158 3.08629 31.6498 3.099L58.9844 3.12201L59.3803 3.51786ZM17.5009 11.2911C17.3603 11.3492 17.1286 11.5449 16.9862 11.726L16.7273 12.0551V31.4118V50.7685L17.0807 51.164L17.4339 51.5595L27.4039 51.5967C34.5786 51.6233 37.6282 51.594 38.2813 51.4922C42.0903 50.8982 45.0687 48.1818 46.0292 44.4259C46.2376 43.611 46.2742 43.0814 46.2742 40.8775C46.2742 37.7844 46.1515 37.0832 45.3016 35.3188C44.8366 34.3534 44.5166 33.8866 43.7923 33.1167C43.2867 32.5793 42.6304 31.9862 42.3337 31.7987C42.0369 31.6114 41.7942 31.4307 41.7942 31.3974C41.7942 31.3641 42.0369 31.1834 42.3337 30.9961C42.6304 30.8086 43.2867 30.2155 43.7923 29.6781C44.5166 28.9082 44.8366 28.4414 45.3016 27.476C46.1515 25.7116 46.2742 25.0104 46.2742 21.9173C46.2742 19.7134 46.2376 19.1838 46.0292 18.3689C45.5549 16.5144 44.6035 14.9215 43.2056 13.6417C42.1872 12.7094 41.1473 12.1055 39.7351 11.6259L38.5847 11.2353L28.1707 11.2103C22.4429 11.1967 17.6416 11.233 17.5009 11.2911ZM38.7664 14.1253C41.0955 14.8904 42.8894 16.7902 43.424 19.0583C43.6767 20.1307 43.6759 23.7772 43.4226 24.8519C42.9551 26.8352 41.6654 28.444 39.8407 29.3198C39.2831 29.5875 38.5036 29.8647 38.1084 29.9359C37.6408 30.0202 34.2477 30.0654 28.3906 30.0654H19.3914V21.9465V13.8277L28.7459 13.8671C37.3764 13.9035 38.152 13.9234 38.7664 14.1253ZM38.1084 32.8589C39.0666 33.0314 40.6188 33.797 41.387 34.4758C42.1531 35.1529 42.7901 36.0949 43.2125 37.176C43.5131 37.9455 43.5364 38.1489 43.5825 40.4189C43.6218 42.35 43.5923 43.0098 43.4372 43.6747C42.8947 46 41.1218 47.8958 38.7664 48.6695C38.152 48.8713 37.3764 48.8913 28.7459 48.9277L19.3914 48.9671V40.8483V32.7294H28.3906C34.2477 32.7294 37.6408 32.7746 38.1084 32.8589Z"
                                  fill="#7E22CE"
                                  fill-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">No Bootstrap Dependency</h3>
                </div>
                <div class="feature_box">
                    <div class="feature-img">
                        <svg class="feature-img-svg8" fill="none" height="80" viewbox="0 0 57 63" width="80"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask fill="white" id="path-1-inside-1_310_57">
                                <path clip-rule="evenodd"
                                      d="M15.2625 4.45737L11.2324 8.48761V14.1449V19.8022L6.84276 19.8025C1.9174 19.8026 1.96741 19.7947 1.25078 20.6914L0.878897 21.1567L0.844022 34.8594C0.824768 42.3958 0.841115 48.7403 0.880229 48.9581C0.961846 49.4125 1.54358 50.1273 2.04128 50.3846C2.31931 50.5285 3.17798 50.5601 6.80268 50.5601H11.2248L11.2588 55.6247L11.293 60.6894L11.6271 61.194C11.8108 61.4716 12.1882 61.8489 12.4658 62.0326L12.9705 62.3667L28.2372 62.4015C39.3629 62.4268 43.64 62.3988 44.0057 62.2985C44.6685 62.1166 45.2998 61.581 45.6121 60.936C45.8562 60.4321 45.8652 60.2363 45.8652 55.4866V50.5601H50.2912C53.8655 50.5601 54.78 50.5272 55.0441 50.3892C55.4831 50.1598 55.9927 49.6075 56.1505 49.1899C56.2352 48.9656 56.2793 46.7785 56.2793 42.7966V36.7444L55.982 36.4472C55.6255 36.0906 55.1981 36.0666 54.8093 36.3813L54.5234 36.6127L54.4629 42.6479L54.4023 48.6831L53.3428 48.7181L52.2832 48.7531V43.6198C52.2832 38.4932 52.2828 38.4859 52.0098 38.0751C51.8594 37.8487 51.4937 37.541 51.197 37.3911L50.6576 37.1187H28.5488H6.44001L5.90066 37.3911C5.60398 37.541 5.23827 37.8487 5.08787 38.0751C4.81481 38.4859 4.81444 38.4932 4.81444 43.6198V48.7531L3.75487 48.7181L2.6953 48.6831V35.1812V21.6792H28.5488H54.4023L54.4629 27.4722L54.5234 33.2652L54.8093 33.4966C55.2236 33.8319 55.7252 33.7943 56.0315 33.4048C56.2831 33.085 56.2855 33.0178 56.2523 27.1191L56.2188 21.1566L55.8469 20.6913C55.1301 19.7947 55.1803 19.8026 50.2549 19.8025L45.8652 19.8022V11.1402C45.8652 1.67657 45.8884 2.00352 45.1625 1.22937C44.9829 1.0378 44.618 0.780113 44.3516 0.65684C43.8987 0.447105 43.4288 0.432332 37.1108 0.42991L30.3542 0.427246L30.057 0.724531C29.8936 0.888008 29.7598 1.16289 29.7598 1.33545C29.7598 1.50801 29.8936 1.78289 30.057 1.94637L30.3542 2.24365H37.0012C43.2868 2.24365 43.6589 2.25564 43.8484 2.46513C44.0309 2.66675 44.0488 3.4512 44.0488 11.2444V19.8022H28.5488H13.0488V14.847V9.89181L15.9248 9.85185C18.7113 9.81322 18.8168 9.80281 19.3137 9.51824C19.9345 9.16271 20.3802 8.54695 20.5474 7.81396C20.6158 7.51426 20.6731 6.13839 20.6748 4.75635L20.6777 2.24365H23.7105H26.7434L27.0406 1.94637C27.2041 1.78289 27.3379 1.50801 27.3379 1.33545C27.3379 1.16289 27.2041 0.888008 27.0406 0.724531L26.7434 0.427246H23.0181H19.2928L15.2625 4.45737ZM18.8016 7.2958C18.6765 7.98253 18.4306 8.05615 16.2624 8.05615H14.3227L16.5617 5.81434L18.8008 3.57266L18.8367 5.23915C18.8565 6.15583 18.8407 7.08135 18.8016 7.2958ZM45.5625 26.3664C44.0365 26.8998 43.1601 28.5807 43.6089 30.1137C44.4539 33.0011 48.6101 32.9964 49.4555 30.1071C49.9075 28.5628 49.0699 26.9344 47.5472 26.3971C46.973 26.1945 46.093 26.1809 45.5625 26.3664ZM47.3049 28.3466C47.7686 28.7368 47.8963 29.3207 47.6305 29.8347C47.3927 30.2947 47.104 30.4585 46.5312 30.4585C45.931 30.4585 45.6349 30.2744 45.4243 29.7702C45.1912 29.2123 45.3063 28.7264 45.7623 28.3426C46.2445 27.937 46.8198 27.9384 47.3049 28.3466ZM50.4668 40.9331V42.9312H28.5488H6.63085V40.9331V38.9351H28.5488H50.4668V40.9331ZM11.2324 46.7456V48.7437H8.93163H6.63085V46.7456V44.7476H8.93163H11.2324V46.7456ZM44.0488 52.437C44.0488 59.965 44.0437 60.1315 43.8066 60.3687C43.5671 60.6082 43.403 60.6108 28.5488 60.6108C13.6946 60.6108 13.5305 60.6082 13.291 60.3687C13.0539 60.1315 13.0488 59.965 13.0488 52.437V44.7476H28.5488H44.0488V52.437ZM50.4668 46.7456V48.7437H48.166H45.8652V46.7456V44.7476H48.166H50.4668V46.7456ZM16.7249 47.0464C16.4186 47.1711 16.0762 47.6251 16.0762 47.9067C16.0762 48.016 16.1891 48.2491 16.3272 48.4246L16.5781 48.7437H28.5488H40.5196L40.7705 48.4246C41.0973 48.0092 41.0872 47.7438 40.7293 47.3276L40.4371 46.9878L28.7107 46.9643C22.251 46.9513 16.8679 46.9882 16.7249 47.0464ZM16.6046 50.9439C16.3393 51.0723 16.0762 51.52 16.0762 51.8428C16.0762 51.9426 16.21 52.1579 16.3734 52.3214L16.6706 52.6187H28.5488H40.427L40.7242 52.3214C40.8877 52.1579 41.0215 51.9426 41.0215 51.8428C41.0215 51.5025 40.7531 51.0714 40.4575 50.9367C40.0582 50.7548 16.981 50.7617 16.6046 50.9439ZM16.4287 54.9038C16.2261 55.0678 16.1488 55.2561 16.1488 55.5854C16.1488 55.9148 16.2261 56.1031 16.4287 56.2671C16.6977 56.4851 17.1607 56.4937 28.5488 56.4937C39.937 56.4937 40.3999 56.4851 40.669 56.2671C41.0578 55.9522 41.0578 55.2187 40.669 54.9038C40.3999 54.6858 39.937 54.6772 28.5488 54.6772C17.1607 54.6772 16.6977 54.6858 16.4287 54.9038Z"
                                      fill-rule="evenodd"></path>
                            </mask>
                            <path clip-rule="evenodd"
                                  d="M15.2625 4.45737L11.2324 8.48761V14.1449V19.8022L6.84276 19.8025C1.9174 19.8026 1.96741 19.7947 1.25078 20.6914L0.878897 21.1567L0.844022 34.8594C0.824768 42.3958 0.841115 48.7403 0.880229 48.9581C0.961846 49.4125 1.54358 50.1273 2.04128 50.3846C2.31931 50.5285 3.17798 50.5601 6.80268 50.5601H11.2248L11.2588 55.6247L11.293 60.6894L11.6271 61.194C11.8108 61.4716 12.1882 61.8489 12.4658 62.0326L12.9705 62.3667L28.2372 62.4015C39.3629 62.4268 43.64 62.3988 44.0057 62.2985C44.6685 62.1166 45.2998 61.581 45.6121 60.936C45.8562 60.4321 45.8652 60.2363 45.8652 55.4866V50.5601H50.2912C53.8655 50.5601 54.78 50.5272 55.0441 50.3892C55.4831 50.1598 55.9927 49.6075 56.1505 49.1899C56.2352 48.9656 56.2793 46.7785 56.2793 42.7966V36.7444L55.982 36.4472C55.6255 36.0906 55.1981 36.0666 54.8093 36.3813L54.5234 36.6127L54.4629 42.6479L54.4023 48.6831L53.3428 48.7181L52.2832 48.7531V43.6198C52.2832 38.4932 52.2828 38.4859 52.0098 38.0751C51.8594 37.8487 51.4937 37.541 51.197 37.3911L50.6576 37.1187H28.5488H6.44001L5.90066 37.3911C5.60398 37.541 5.23827 37.8487 5.08787 38.0751C4.81481 38.4859 4.81444 38.4932 4.81444 43.6198V48.7531L3.75487 48.7181L2.6953 48.6831V35.1812V21.6792H28.5488H54.4023L54.4629 27.4722L54.5234 33.2652L54.8093 33.4966C55.2236 33.8319 55.7252 33.7943 56.0315 33.4048C56.2831 33.085 56.2855 33.0178 56.2523 27.1191L56.2188 21.1566L55.8469 20.6913C55.1301 19.7947 55.1803 19.8026 50.2549 19.8025L45.8652 19.8022V11.1402C45.8652 1.67657 45.8884 2.00352 45.1625 1.22937C44.9829 1.0378 44.618 0.780113 44.3516 0.65684C43.8987 0.447105 43.4288 0.432332 37.1108 0.42991L30.3542 0.427246L30.057 0.724531C29.8936 0.888008 29.7598 1.16289 29.7598 1.33545C29.7598 1.50801 29.8936 1.78289 30.057 1.94637L30.3542 2.24365H37.0012C43.2868 2.24365 43.6589 2.25564 43.8484 2.46513C44.0309 2.66675 44.0488 3.4512 44.0488 11.2444V19.8022H28.5488H13.0488V14.847V9.89181L15.9248 9.85185C18.7113 9.81322 18.8168 9.80281 19.3137 9.51824C19.9345 9.16271 20.3802 8.54695 20.5474 7.81396C20.6158 7.51426 20.6731 6.13839 20.6748 4.75635L20.6777 2.24365H23.7105H26.7434L27.0406 1.94637C27.2041 1.78289 27.3379 1.50801 27.3379 1.33545C27.3379 1.16289 27.2041 0.888008 27.0406 0.724531L26.7434 0.427246H23.0181H19.2928L15.2625 4.45737ZM18.8016 7.2958C18.6765 7.98253 18.4306 8.05615 16.2624 8.05615H14.3227L16.5617 5.81434L18.8008 3.57266L18.8367 5.23915C18.8565 6.15583 18.8407 7.08135 18.8016 7.2958ZM45.5625 26.3664C44.0365 26.8998 43.1601 28.5807 43.6089 30.1137C44.4539 33.0011 48.6101 32.9964 49.4555 30.1071C49.9075 28.5628 49.0699 26.9344 47.5472 26.3971C46.973 26.1945 46.093 26.1809 45.5625 26.3664ZM47.3049 28.3466C47.7686 28.7368 47.8963 29.3207 47.6305 29.8347C47.3927 30.2947 47.104 30.4585 46.5312 30.4585C45.931 30.4585 45.6349 30.2744 45.4243 29.7702C45.1912 29.2123 45.3063 28.7264 45.7623 28.3426C46.2445 27.937 46.8198 27.9384 47.3049 28.3466ZM50.4668 40.9331V42.9312H28.5488H6.63085V40.9331V38.9351H28.5488H50.4668V40.9331ZM11.2324 46.7456V48.7437H8.93163H6.63085V46.7456V44.7476H8.93163H11.2324V46.7456ZM44.0488 52.437C44.0488 59.965 44.0437 60.1315 43.8066 60.3687C43.5671 60.6082 43.403 60.6108 28.5488 60.6108C13.6946 60.6108 13.5305 60.6082 13.291 60.3687C13.0539 60.1315 13.0488 59.965 13.0488 52.437V44.7476H28.5488H44.0488V52.437ZM50.4668 46.7456V48.7437H48.166H45.8652V46.7456V44.7476H48.166H50.4668V46.7456ZM16.7249 47.0464C16.4186 47.1711 16.0762 47.6251 16.0762 47.9067C16.0762 48.016 16.1891 48.2491 16.3272 48.4246L16.5781 48.7437H28.5488H40.5196L40.7705 48.4246C41.0973 48.0092 41.0872 47.7438 40.7293 47.3276L40.4371 46.9878L28.7107 46.9643C22.251 46.9513 16.8679 46.9882 16.7249 47.0464ZM16.6046 50.9439C16.3393 51.0723 16.0762 51.52 16.0762 51.8428C16.0762 51.9426 16.21 52.1579 16.3734 52.3214L16.6706 52.6187H28.5488H40.427L40.7242 52.3214C40.8877 52.1579 41.0215 51.9426 41.0215 51.8428C41.0215 51.5025 40.7531 51.0714 40.4575 50.9367C40.0582 50.7548 16.981 50.7617 16.6046 50.9439ZM16.4287 54.9038C16.2261 55.0678 16.1488 55.2561 16.1488 55.5854C16.1488 55.9148 16.2261 56.1031 16.4287 56.2671C16.6977 56.4851 17.1607 56.4937 28.5488 56.4937C39.937 56.4937 40.3999 56.4851 40.669 56.2671C41.0578 55.9522 41.0578 55.2187 40.669 54.9038C40.3999 54.6858 39.937 54.6772 28.5488 54.6772C17.1607 54.6772 16.6977 54.6858 16.4287 54.9038Z"
                                  fill="#7E22CE"
                                  fill-rule="evenodd" mask="url(#path-1-inside-1_310_57)" stroke="#7E22CE"
                                  stroke-width="2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-txt">Exact Look On Print Window</h3>
                </div>
            </div>
        </div>
    </section>
    <!-- Features Section End -->

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
