<?php
$DB = 'Prophet.njit.edu';
$DB_USER = 'BX34';
$DB_PASS = 'Xx0SnUuM';
$DB_CHAR = 'course';

$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = Prophet.njit.edu)(PORT = 1521)))(CONNECT_DATA=(SID=course)))" ;

    if($c = OCILogon("BX34", "Xx0SnUuM", $db))
    {
        echo "Successfully connected to Oracle.\n";
        OCILogoff($c);
    }
    else
    {
        $err = OCIError();
        echo "Connection failed." . $err[text];
    }

// check user_id and porfolio_id
	$user_name = "Ultimate";

?>


<!DOCTYPE html>
<html lang="en">
<style>
  body {
      position: relative; 
  }
  #section1 {padding-top:30px;height:500px;color: #333333; background-color: #EAEAEA;}
  #section2 {padding-top:30px;height:750px;color: #333333; background-color: #EAEAEA;}
</style>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://getbootstrap.com/favicon.ico">

    <title>Portfolio Optimization System</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.css" rel="stylesheet">
	<link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  <nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">Portfolio Optimization System</a>
        </div>
		<div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">About POS</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#"><?php echo $user_name;?></a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
  </div>
</nav>    

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<div id="section1" class="container-fluid">
  <h2>Execute SQL Directly</h2>
  <p>Please input a SQL clause:</p>
  <p><form class="form-group" method="post" action="SqlDirect.php"><input class="form-control" type="text" placeholder="SQL Clause" id="sql_clause" name="sql_clause"></p>
  <p><button class="btn btn-success" type="submit" id="btn_newPortfolioName" onClick="return creatPflo()">Execute Now</button></form></p>
  <p><?php echo $error_msg_1 ?></p>
</div>

</div>
</body></html>

<?php
require "conn_close.php";
?>