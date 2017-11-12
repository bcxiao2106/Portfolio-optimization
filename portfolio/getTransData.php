<?php
  require("conn.php");
?>

<?php
  //if(isset($_POST['pid'])) {
    $pid = $_POST['pid'];
      
      $sql = "SELECT
      pflo_transaction.stk_id, pflo_transaction.oprt_type, pflo_transaction.share,
      pflo_transaction.price, pflo_transaction.cash_value, pflo_transaction.date,
      stk_info.stk_name, stk_info.symbol, stk_info.stk_id
      FROM
      pflo_transaction, stk_info
      WHERE
      pflo_transaction.pflo_id = $pid
      AND stk_info.stk_id = pflo_transaction.stk_id";        
    $result = $conn->query($sql);
    
    
    if($result->num_rows > 0){
	  while($row = $result->fetch_assoc()){
	      echo "<tr>";
		  echo "<td>$row[stk_name]</td>";
		  echo "<td>$row[symbol]</td>";
		  echo "<td>$row[oprt_type]</td>";
		  echo "<td>$row[date]</td>";
		  echo "<td>$row[share]</td>";
		  echo "<td>$row[price]</td>";
		  echo "<td>$row[cash_value]</td>";
		  echo "</tr>";
	    
	  } 
	} 
	
	$sql1 = "SELECT
      pflo_transaction.oprt_type, 
      pflo_transaction.cash_value, pflo_transaction.date
      FROM
      pflo_transaction
      WHERE
      pflo_transaction.pflo_id = $pid";        
    $result1 = $conn->query($sql1);
    if($result1->num_rows > 0){
	  while($row1 = $result1->fetch_assoc()){
	    if($row1[oprt_type] == "Deposit" || $row1[oprt_type] == "Withdraw") {
	      echo "<tr>";
		  echo "<td></td>";
		  echo "<td></td>";
		  echo "<td>$row1[oprt_type]</td>";
		  echo "<td>$row1[date]</td>";
		  echo "<td></td>";
		  echo "<td></td>";
		  echo "<td>$row1[cash_value]</td>";
		  echo "</tr>";
	    }
	    
	  }
	}
  //}
  
  require "conn_close.php";
?>