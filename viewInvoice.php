<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();


if(!isset($_SESSION['uid'])){
    ?>
    <script>
        window.location.href="index.php";
    </script>
    <?php
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

    <!-- DataTables CSS for Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
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
                                    <h2 class="fw-bold">View Invoice</h2>
                                </div>

                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="table-dark">
                                        <tr>
                                            <th>SL</th>
                                            <th>Bill To</th>
                                            <th>Date</th>
                                            <th>Inv No</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $query="SELECT * FROM `invoice` where uid='{$_SESSION['uid']}'";
                                        $data=$db_handle->selectQuery($query);
                                        $row=$db_handle->numRows($query);

                                        for($i=0;$i<$row;$i++){
                                            ?>
                                            <tr>
                                                <td><?= $i + 1; ?></td>
                                                <td><?= $data[$i]['ibillto']; ?></td>
                                                <td><?= $data[$i]['invoiceDate']; ?></td>
                                                <td><?= $data[$i]['iinv_no']; ?></td>
                                                <td>
                                                    <a href="invoice<?= $data[$i]['inv_view']; ?>.php?id=<?= $data[$i]['sharable_url']; ?>" class="btn btn-sm btn-info me-1 text-white">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a onclick="copyCurrentPageURL('<?= $data[$i]['inv_view']; ?>','<?= $data[$i]['sharable_url']; ?>');" class="btn btn-sm btn-primary me-1">
                                                        <i class="fas fa-share"></i>
                                                    </a>
                                                    <a href="editInvoice.php?id=<?= $data[$i]['sharable_url']; ?>" class="btn btn-sm btn-warning me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="deleteInvoice.php?id=<?= $data[$i]['sharable_url']; ?>" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
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

<!-- DataTables JS and Bootstrap 5 integration -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    function copyCurrentPageURL(inv,id) {
        const url = 'https://invoice.com/invoice'+inv+'.php?id='+id;
        const text = 'Share Your Invoice!';

        if (navigator.share) {
            // If the Web Share API is supported
            navigator.share({
                title: 'Sharing Software',
                text: text,
                url: url,
            })
                .then(() => console.log('Successfully shared!'))
                .catch((error) => console.error('Error sharing:', error));
        } else {
            // If Web Share API is not supported, show custom share options
            navigator.clipboard.writeText(window.location.href)
                .then(() => {
                    console.log('Invoice copied to clipboard!');
                    alert('Invoice copied to clipboard!');
                })
                .catch(err => {
                    console.error('Failed to copy Invoice: ', err);
                    alert('Failed to copy Invoice. Please try again.');
                });
        }
    }


    function visibleProfile(value) {
        let profile = document.getElementById('profile');

        if (value == 0)
            profile.style.display = 'none';
        else
            profile.style.display = 'block';
    }


    $(document).ready(function () {
        $('#example').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
        });
    });


</script>
</body>
</html>
