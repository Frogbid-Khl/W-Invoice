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
        <script>
            alert('Unlocked Invoice Successful.');
            window.location.href="viewInvoice.php";
        </script>
        <?php
    }else{
        ?>
        <script>
            alert('You not have enough credit.');
            window.location.href="viewInvoice.php";
        </script>
        <?php
    }
}
?>
