<?php
session_start();
session_destroy();
session_unset();
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
        text: 'Logout Successful.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(function() {
        window.location.href = "index.php";
    });
</script>
</body>
</html>
