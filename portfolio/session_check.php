<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//print_r($_SESSION);
define('user_name','user_name');
if($_SESSION["user_name"] == "" || $_SESSION["user_name"] == null){
	echo "<script language=\"javascript\">";
	echo "alert(\"Please login again!\");";
	echo "location.href=\"index.html\";";
	echo"</script>";
}
?>