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

if (isset($_POST['saveInvoice'])) {

    $uid=$_SESSION['uid'];
    $query="SELECT * FROM `user` WHERE `uid`={$_SESSION['uid']}";

    $data=$db_handle->selectQuery($query);
    $credit=$data[0]['credit'];

    if($credit>0){
        $query="update `user` set  credit=credit-1 WHERE `uid`={$_SESSION['uid']}";
        $update=$db_handle->insertQuery($query);

        $from = $_POST['from'] ?? '';
        $billto = $_POST['billto'] ?? '';
        $shipto = $_POST['shipto'] ?? '';
        $invoice = $_POST['invoice'] ?? '';
        $invoiceDate = $_POST['invoiceDate'] ?? '';
        $po = $_POST['po'] ?? '';
        $dueDate = $_POST['dueDate'] ?? '';
        $terms = $_POST['terms'] ?? '';

        $invoiceOption = $_POST['invoiceOption'] ?? '1';

        $pname = $_POST['pname'] ?? [];
        $unitPrice = $_POST['unitPrice'] ?? [];
        $qty = $_POST['qty'] ?? [];
        $amount = $_POST['amount'] ?? [];
        $tax = $_POST['tax'] ?? '';

        $updated_at = date("Y-m-d H:i:s");
        $sharable_url = $_POST['sharable_url'] ?? '';

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

        // UPDATE main invoice record
        $updateQuery = "
        UPDATE `invoice` SET
            `ifrom` = '$from',
            `inv_view` = '$invoiceOption',
            `ibillto` = '$billto',
            `ishipto` = '$shipto',
            `ilogo` = '$logo',
            `iinv_no` = '$invoice',
            `ipo` = '$po',
            `idue_date` = '$dueDate',
            `itoc` = '$terms',
            `isignature` = '$signature',
            `updated_at` = '$updated_at'
        WHERE `sharable_url` = '$sharable_url'
    ";

        $update = $db_handle->insertQuery($updateQuery);

        // Remove existing invoice line items before adding updated ones
        $db_handle->insertQuery("DELETE FROM `invoice_detail` WHERE `isharable_url` = '$sharable_url'");

        // Insert updated line items
        foreach ($pname as $key => $name) {
            $price = $unitPrice[$key];
            $quantity = $qty[$key];
            $line_amount = $amount[$key];
            $line_tax = $tax[$key];

            $db_handle->insertQuery("INSERT INTO `invoice_detail`(`uid`, `isharable_url`, `pname`, `price`, `qty`, `amount`, `tax`, `inserted_at`, `updated_at`) 
        VALUES ('$uid','$sharable_url','$name','$price','$quantity','$line_amount','$line_tax','$updated_at','$updated_at')");
        }

        if ($update) {
            ?>
            <script>
                alert('Invoice Updated');
                window.location.href = "invoice" + "<?php echo $invoiceOption; ?>" + ".php?id=<?php echo $sharable_url; ?>";
            </script>
            <?php
        }
    }else{
        ?>
        <script>
            alert('Reload The Credit First for Updated');
        </script>
        <?php
    }

}

$invoice='';
$invoiceData='';

if (isset($_GET['id'])) {
    $query="SELECT * FROM `invoice` where sharable_url='{$_GET['id']}'";
    $invoice=$db_handle->selectQuery($query);

    $query="SELECT * FROM `invoice_detail` where isharable_url='{$_GET['id']}'";
    $invoiceData=$db_handle->selectQuery($query);
    $invoiceDataRow=$db_handle->numRows($query);
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
                <div class="hero-full-second pb-5 pt-5" id="create-invoice">
                    <div class="card p-4 bg-light">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="container py-5">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Edit Your Custom Invoice</h2>
                                </div>
                                <input type="hidden" name="sharable_url" value="<?= $invoice[0]['sharable_url'] ?>" required>

                                <div class="row g-4">
                                    <!-- Sender and Addresses -->
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <label class="form-label" for="from">From</label>
                                            <textarea class="form-control" name="from" id="from"
                                                      placeholder="Your Company or Name, Address"
                                                      rows="5" required><?= $invoice[0]['ifrom'] ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="billto">Bill To</label>
                                            <textarea class="form-control" name="billto" id="billto"
                                                      placeholder="Customer Billing Address"
                                                      rows="5" required><?= $invoice[0]['ibillto'] ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="shipto">Ship To</label>
                                            <textarea class="form-control" name="shipto" id="shipto"
                                                      placeholder="Shipping Address (Optional)"
                                                      rows="5" required><?= $invoice[0]['ishipto'] ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Invoice Details -->
                                    <div class="col-lg-4">
                                        <div class="mb-4">
                                            <label class="form-label" for="logo">Logo</label>
                                            <input type="hidden" name="inlogo" value="<?= $invoice[0]['ilogo'] ?>"/>
                                            <div id="logoPreviewWrapper" class="<?= !empty($invoice[0]['ilogo']) ? '' : 'd-none' ?>">
                                                <div class="position-relative d-inline-block">
                                                    <img src="<?= $invoice[0]['ilogo'] ?>" alt="Logo" class="img-fluid" id="logoPreview" />
                                                    <button type="button" class="btn-close position-absolute top-0 end-0" onclick="removeImage('logo')"></button>
                                                </div>
                                            </div>

                                            <div id="logoUpload" class="<?= empty($invoice[0]['ilogo']) ? '' : 'd-none' ?>">
                                                <input class="form-control border-0" name="logo" id="logo" style="width: 90%;" type="file">
                                            </div>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="invoice">Invoice #</label>
                                            <input class="form-control" id="invoice" name="invoice" value="<?= $invoice[0]['iinv_no'] ?>" placeholder="e.g. INV-1001"
                                                   type="text" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="invoiceDate">Invoice Date</label>
                                            <input class="form-control" required name="invoiceDate" id="invoiceDate" value="<?= date("Y-m-d",strtotime($invoice[0]['invoiceDate'])) ?>" type="date">

                                            <script>
                                                // Get today's date in YYYY-MM-DD format
                                                const today = new Date().toISOString().split('T')[0];
                                                // Set it as the value of the input field
                                                document.getElementById('invoiceDate').value = today;
                                            </script>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="po">P.O #</label>
                                            <input class="form-control" name="po" id="po" value="<?= $invoice[0]['ipo'] ?>" placeholder="Optional" type="text">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="dueDate">Due Date</label>
                                            <input class="form-control" name="dueDate" id="dueDate" value="<?= date("Y-m-d",strtotime($invoice[0]['idue_date'])) ?>" type="date">

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
                                                <?php
                                                $subTotal = 0;
                                                $tax=0;
                                                for ($i=0;$i<$invoiceDataRow;$i++){
                                                    $total = $invoiceData[$i]['qty'] * $invoiceData[$i]['price'];
                                                    $tax+=($invoiceData[$i]['tax']/100)*$total;
                                                    $subTotal += $total;
                                                    ?>
                                                    <div class="product-row mb-3 custom-grid">
                                                        <div class="form-floating">
                                                            <input class="form-control pname" name="pname[]" value="<?= $invoiceData[$i]['pname'] ?>" required placeholder="Product Name" type="text">
                                                            <label>Product Name</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <input class="form-control unitPrice" name="unitPrice[]" value="<?= $invoiceData[$i]['price'] ?>" required placeholder="Unit Price" type="number">
                                                            <label>Unit Price</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <input class="form-control qty" name="qty[]" placeholder="Quantity" value="<?= $invoiceData[$i]['qty'] ?>" required type="number">
                                                            <label>Quantity</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <input class="form-control amount" name="amount[]" placeholder="Amount" value="<?= $invoiceData[$i]['amount'] ?>" required readonly type="number">
                                                            <label>Amount</label>
                                                        </div>
                                                        <div class="form-floating">
                                                            <input class="form-control tax" name="tax[]" placeholder="Tax" value="<?= $invoiceData[$i]['tax'] ?>" type="text">
                                                            <label>Tax</label>
                                                        </div>
                                                        <div class="delete-wrapper d-flex align-items-center">
                                                            <button class="btn btn-danger delete-btn" type="button">X</button>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>

                                            </div>

                                            <button class="btn btn-outline-primary w-100 mt-3" id="add-product-btn" type="button">
                                                + Add New Item
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                    $grandTotal = $subTotal + $tax;
                                    ?>
                                    <div class="col-12 mt-4">
                                        <div class="row">
                                            <div class="col-lg-6 ms-auto">
                                                <div class="p-4 rounded shadow-sm border bg-light">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0">Subtotal</h5>
                                                        <h5 class="mb-0" id="subtotal"><?= $subTotal ?></h5>
                                                    </div>
                                                    <hr>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="mb-0 fw-bold">Total (BDT)</h4>
                                                        <h4 class="mb-0 fw-bold text-success" id="total"><?= $grandTotal ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8 mt-4">
                                        <div class="form-floating">
                                            <textarea class="form-control" name="terms" id="terms" placeholder="Leave a comment here"
                                                      style="height: 180px"><?= $invoice[0]['itoc'] ?></textarea>
                                            <label for="terms">Terms and Condition</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-4">
                                            <label class="form-label" for="logo">Signature</label>
                                            <input type="hidden" name="insign" value="<?= $invoice[0]['isignature'] ?>"/>
                                            <div id="signaturePreviewWrapper" class="<?= !empty($invoice[0]['isignature']) ? '' : 'd-none' ?>">
                                                <div class="position-relative d-inline-block">
                                                    <img src="<?= $invoice[0]['isignature'] ?>" alt="Signature" class="img-fluid" id="signaturePreview" />
                                                    <button type="button" class="btn-close position-absolute top-0 end-0" aria-label="Remove"
                                                            onclick="removeImage('signature')"></button>
                                                </div>
                                            </div>

                                            <div id="signatureUpload" class="<?= empty($invoice[0]['isignature']) ? '' : 'd-none' ?>">
                                                <input class="form-control border-0" name="signature" id="signature" style="width: 90%;" type="file">
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


                                            function updateAmounts() {
                                                    let subtotal = 0;
                                                    let taxTotal = 0;

                                                    document.querySelectorAll('.product-row').forEach(row => {
                                                        const price = parseFloat(row.querySelector('.unitPrice').value) || 0;
                                                        const qty = parseFloat(row.querySelector('.qty').value) || 0;
                                                        const tax = parseFloat(row.querySelector('.tax').value) || 0;

                                                        const amount = price * qty;
                                                        row.querySelector('.amount').value = amount.toFixed(2);

                                                        subtotal += amount;
                                                        taxTotal += (amount * tax / 100);
                                                    });

                                                    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                                                    document.getElementById('total').textContent = (subtotal + taxTotal).toFixed(2);
                                                }

                                                // Trigger update on change
                                                document.addEventListener('input', function (e) {
                                                    if (e.target.classList.contains('unitPrice') ||
                                                        e.target.classList.contains('qty') ||
                                                        e.target.classList.contains('tax')) {
                                                        updateAmounts();
                                                    }
                                                });

                                                // Trigger update on page load just in case
                                                document.addEventListener('DOMContentLoaded', updateAmounts);
                                            </script>

                                        </div>
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
