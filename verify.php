<?php
session_start();
require_once('connection/dbController.php');
$db_handle = new DBController();

if(isset($_GET['hash'])){

    $query = "update `user` set status=1 WHERE `hash_code`='{$_GET['hash']}'";
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
            text: 'Verify Successful.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = "login.php";
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
            text: 'Something went wrong.',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = "index.php";
        });
    </script>
    </body>
    </html>
    <?php
}