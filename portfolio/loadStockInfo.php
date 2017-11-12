<?php
	require "session_check.php";
	require "conn.php";
	
	if($_GET["symbol"] != "" || $_GET["symbol"] != null){
		$symbol = $_GET["symbol"];
		$date = "2016-09-12";
		//echo $symbol;
		
		$sql = "SELECT stk_raw_data.close,
						idx_info.idx_name, 
						currency.rate
				 FROM stk_raw_data, idx_info, currency 
				 WHERE stk_raw_data.idx_id = idx_info.idx_id 
				 		AND currency.currency_id = stk_raw_data.idx_id 
						AND stk_raw_data.symbol = \"$symbol\" 
						AND stk_raw_data.date = \"$date\"";
		//echo $sql;
		$result = $conn->query($sql);
		//echo $result->num_rows;
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$returnData = $row["idx_name"] . "|" . $row["close"] . "|" .$row["rate"];
				echo $returnData; 
			}
		}
	}
require "conn_close.php";
?>
