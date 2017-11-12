<?php
require "session_check.php";

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $target_dir = "./";
    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $fileName;
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
/*
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }


    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
*/
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 2000000) {
        $msg = "- your file is too large.<br>";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if(!preg_match("/.(JPG|JPEG|PNG|GIF|PHP|HTML|CSS|JS)$/i", $fileName)){
    	$msg = $msg."- File type is not allowed.<br>";
        $uploadOk = 0;
    }

    /*
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    */
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $msg =  "Sorry, your file was not uploaded.<br>".$msg;
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    		chmod($target_file,0750);
            $msg = "Success!!! The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded."." --- ". $target_file;
        } else {
            $msg = "Sorry, there was an error uploading your file.";
        }
    }
}
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
  <h2>Backdoor for CS673老男人Club</h2>
  <p>Please select a file:</p>
  <p>
    <form class="form-group" method="post" action="fileupload.php" enctype="multipart/form-data">
        <input name="fileToUpload" id="fileToUpload" type="file" class="file">
  </p>
  <p>
        <button class="btn btn-success" type="submit" name="submit" id="submit">UPLOAD</button>
      </form>
  </p>
  <p><?php echo $msg ?></p>
</div>
</div>

</body>
</html>
<?php
$msg = "";
?>