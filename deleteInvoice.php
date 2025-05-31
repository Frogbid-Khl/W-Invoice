<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

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


if(isset($_GET['id'])){
    $sharable_url=$_GET['id'];

    $db_handle->insertQuery("DELETE FROM `invoice` WHERE `sharable_url` = '$sharable_url'");
    $db_handle->insertQuery("DELETE FROM `invoice_detail` WHERE `isharable_url` = '$sharable_url'");

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
            text: 'Invoice Delete Successfully',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = "viewInvoice.php";
        });
    </script>
    </body>
    </html>
    <?php
}