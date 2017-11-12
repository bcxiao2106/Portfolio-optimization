<?php
  require("conn.php");
?>

<?php
  if(isset($_POST['stk'])) {
    $symbol = $_POST['stk'];
    $price = 0;
    
    if($symbol != null) {
    
      $sql = "SELECT stk_info.current_price FROM stk_info WHERE stk_info.symbol = \"$symbol\"";
      $result = $conn->query($sql);
      if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
		  $price = $row[current_price];
		}
		echo $price;
	  } else {
	    echo "<p>Cannot find data</p>";
	  }
    }
    
  }
  

  require "conn_close.php";
?>