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
    $sharableUrl=$_GET['id'];
    $uid = $_SESSION['uid'];
    $select=$db_handle->selectQuery("select * from `user` where `uid`='$uid'");
    $credit = $select[0]['credit'];
    if($credit>4) {
        $query = "update `user` set  credit=credit-5 WHERE `uid`={$uid}";
        $update = $db_handle->insertQuery($query);

        $query = "update `invoice` set istatus=0 WHERE `sharable_url`='$sharableUrl'";
        $update = $db_handle->insertQuery($query);

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
                text: 'Unlocked Invoice Successful.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = "viewInvoice.php";
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
                text: 'You not have enough credit.',
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
