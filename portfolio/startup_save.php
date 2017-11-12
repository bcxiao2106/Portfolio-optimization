<?php
	require "session_check.php";
	require "conn.php";
	$user_id = $_SESSION['user_id'];
	$user_name = $_SESSION['user_name'];
	
	if($_POST["pflo_name"] != "" || $_POST["pflo_name"] != null){
		$pflo_name = $_POST["pflo_name"];
		$newDeposit = $_POST["newDeposit"];
		$balance = $newDeposit;
		
		$sql = "SELECT * FROM portfolio WHERE pflo_name=\"$pflo_name\" AND user_id=\"$user_id\"";
		echo $sql;
		$result = $conn->query($sql);
		echo $result->num_rows;
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$pflo_id = $row["pflo_id"];
				echo $pflo_id;
			}
		}
		for($x=1;$x<11;$x++){
			//get stk_id, idx_id
			$symbol = $_POST["trans_symbol_$x"];
			if($symbol == "" || $symbol == null) continue;
			$sql = "SELECT * FROM stk_info WHERE symbol=\"$symbol\"";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$stk_id = $row['stk_id'];
					$idx_id = $row['idx_id'];
				}
			}
			
			$share = $_POST["trans_share_$x"];
			$rate = $_POST["trans_rate_$x"];
			$price = $_POST["trans_price_$x"];
			$value = $_POST["trans_value_$x"];
			$date = $_POST["trans_date_$x"];
			$balance = $balance - $value;
			$sql = "INSERT INTO pflo_stk_info (pflo_id,stk_id,share,currency_id) VALUES ($pflo_id,$stk_id,$share,$idx_id)";
			if ($conn->query($sql) === TRUE) {
					echo " ";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			$sql = "INSERT INTO pflo_transaction (pflo_id,stk_id,oprt_type,share,price,cash_value,date) VALUES ($pflo_id,$stk_id,\"Buy\",$share,$price,$value,\"$date\")";
			if ($conn->query($sql) === TRUE) {
					echo " ";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			$sql = "UPDATE portfolio set cash_balance = $balance where pflo_id = $pflo_id";
			if ($conn->query($sql) === TRUE) {
					echo " ";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			 $sql = "INSERT INTO pflo_trans_result (pflo_id, stk_id, date, share, pur_price) VALUES ($pflo_id,$stk_id,\"$date\",$share,$price)";
			 if ($conn->query($sql) === TRUE) {
					echo " ";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
	}
	echo "<script language=\"javascript\">";
	//echo "alert(\"All stocks have been saved!\");";
	echo "parent.window.location.href=\"main.php?pid=$pflo_id\";";
	echo "</script>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>无标题文档</title>
</head>

<body>
</body>
</html>
<?php 
require "conn_close.php";
?>
