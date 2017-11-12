<?php
  require("conn.php");
?>
<?php 
	
	$pid = $_POST['pid'];
	$symbol = $_POST['symbol'];
	$sql = "SELECT
	stk_info.stk_id,
	stk_info.symbol,
	pflo_trans_result.date,
	pflo_trans_result.share,
	pflo_trans_result.pur_price
	FROM 
	stk_info, pflo_trans_result
	WHERE
	stk_info.symbol = \"$symbol\"	
	AND pflo_trans_result.pflo_id = $pid
	AND stk_info.stk_id = pflo_trans_result.stk_id";
	$count = 1;
	$result = $conn->query($sql);
	if($result->num_rows > 0){
	  while($row = $result->fetch_assoc()){
	    echo "<tr>";
		echo "<td>$row[symbol]</td>";
		echo "<td>$row[date]</td>";
		echo "<td>$row[pur_price]</td>";
		echo "<td>$row[share]</td>";
		echo "<td><input id=$count type='number' max=$row[share] min='0' value='0'></td>";
		echo "</tr>";
		$count++;
	  }
	}
	else {
		echo "<p>";
		echo "You don't have this stock in your porfolio.";
		echo "</p>";
	}
	require "conn_close.php";
?>