<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>Portfolio</title>
<style type="text/css">
<!--
body {
	background-color: #666666;
}
-->
</style><body>
<?php
require "conn.php";
define('user_name', 'user_name');
define('user_id', 'user_id');

// check username and password
$user_name = $_POST["user_name"];
$passwd = $_POST["pwd"];
$sql = "SELECT * FROM user_info WHERE user_name =\"$user_name\" AND passwd = \"$passwd\"";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
	$url = "index.html";
	echo "<script language=\"javascript\"> alert(\"Wrong username or password!\"); location.href=\"$url\"; </script>";
} else {
	$row = $result->fetch_assoc();
	//Start session
	$user_id = $row[user_id];
	session_start();
	$_SESSION["user_name"] = $user_name;
	$_SESSION["user_id"] = $user_id;
	
	//print_r($_SESSION);
	$sql = "SELECT * FROM portfolio WHERE user_id = $user_id";
	$result = $conn->query($sql);
	
	$isFirst = $result->num_rows;
	
	if($isFirst == 0){//no portfolio found = new user, go to startup
		$url = "startup_wizard.php";
		echo "<script language=\"javascript\">";
		echo "location.href=\"$url\";";
		echo "</script>";
	} else {
		$url = "main.php";
		echo "<script language=\"javascript\">"; 
		echo "location.href=\"$url\";";
		echo "</script>";
	}
}
require "conn_close.php";
?> 

</body>
</html> 