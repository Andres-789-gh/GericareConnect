<?php
session_start(); 
session_unset(); 
session_destroy();
header("Location: ../../views/index-login/htmls/index.php"); 
exit();
?>