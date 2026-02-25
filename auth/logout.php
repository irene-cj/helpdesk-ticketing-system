<?php
session_start();
session_destroy();
header("Location: /helpdesk/auth/login.php");
exit();
?>