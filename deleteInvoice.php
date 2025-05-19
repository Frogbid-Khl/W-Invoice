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


if(isset($_GET['id'])){
    $sharable_url=$_GET['id'];

    $db_handle->insertQuery("DELETE FROM `invoice` WHERE `sharable_url` = '$sharable_url'");
    $db_handle->insertQuery("DELETE FROM `invoice_detail` WHERE `isharable_url` = '$sharable_url'");

    ?>
    <script>
        alert("Invoice Delete Successfully")
        window.location.href="viewInvoice.php";
    </script>
    <?php
}