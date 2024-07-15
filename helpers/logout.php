<?php
    setcookie('mobile', '', time() + (86400 * 30), "/");
    setcookie('namefull', '', time() + (86400 * 30), "/");
    header("location: ../login.php");

?>