<?php
require "session_check.php";
require "conn.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if($_POST["pflo_name"] != "" || $_POST["pflo_name"] != null){
		$pflo_name = $_POST["pflo_name"];
		$user_id = $_SESSION["user_id"];
		
		//Query for existing portfolio name
		$sql = "select pflo_name from portfolio where pflo_name = '$pflo_name' and user_id = $user_id";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0){//existing name
			echo "<script language=\"javascript\">";
			echo "alert(\"Please Change Another Name !\");";
			echo "location.href=\"modify_portfolio.php\";";
			echo"</script>";
		} else {//modify  portfolio 
			session_start();
            if($submit=="submit"){ 
                 if (portfolio_exists( $pflo_name))
				 { 
                if (str_replace($pflo_name,$temppflo_name )) //rename the old portfolio
                    {echo "Changed Successfully!";} 
                  }else{ print $pflo_name."no this pflo!<br>" ; } 
            } 
			$sql = "update portfolio SET pflo_name=$pflo_name where user_id=$user_id";
			if ($conn->query($sql) === TRUE) {
				echo "Portfolio modified successfully";
				echo "<script language=\"javascript\">";
				echo "window.opener.location.reload();";
				echo "window.close();";
				echo"</script>";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://getbootstrap.com/favicon.ico">

    <title>Portfolio Management System</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>
	<script language="javascript">
		function checkForm(){
			var pflo_name = document.forms["modifyPfloFrm"]["pflo_name"].value;
			if(pflo_name == null || pflo_name == ""){
				alert("Please input a new portfolio name!");
				//location.href = "index.html";
				return false;
			}
		}
		
		function pfloNameChk(){
			alert("Please choose another name!");
		}
	</script>
    <link href="./css/carousel.css" rel="stylesheet">
  <style type="text/css">
<!--
body {
	background-color: #5A5A5A;
}
-->
</style></head>
  <body>
  <br>
   	<form class="form-group" name="modifyPfloFrm" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
   <div class="form-group" align="center">
   	<input type="text" placeholder="New Portfolio Name" class="form-control" name="pflo_name">
	</div>
	<div class="form-group" align="center">
   <button type="submit" class="btn btn-success" onClick="return checkForm()">Save & Close</button>
   </div>
   </form>

       


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./js/jquery.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="./js/bootstrap.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="./js/holder.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  

<svg xmlns="http://www.w3.org/2000/svg" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="none" style="display: none; visibility: hidden; position: absolute; top: -100%; left: -100%;"><defs><style type="text/css"></style></defs><text x="0" y="25" style="font-weight:bold;font-size:25pt;font-family:Arial, Helvetica, Open Sans, sans-serif">500x500</text></svg></body></html>

<?php
require "conn_close.php";
?>