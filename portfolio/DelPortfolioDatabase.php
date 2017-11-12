<?php
	require "session_check.php";
	require "conn.php";
	$user_id = $_SESSION['user_id'];
	$user_name = $_SESSION['user_name'];
	$pflo_id = $_GET['pid'];



	if($_POST['selectportfolio'] != "" || $_POST['selectportfolio'] != null){
		$trans_id = $_POST['selectportfolio'];
		transportfolio($pflo_id,$trans_id,$user_id);
		deleteportfolio($pflo_id,$user_id);
		echo "<script>alert('Portfolio deleted!'); location.href=\"main.php\";</script>";
	}else{
		deleteportfolio($pflo_id,$user_id);
		echo "<script>alert('Portfolio deleted!'); location.href=\"startup_wizard.php\";</script>";
	}
	function deleteportfolio($pflo_id,$user_id){
	require "conn.php";
	echo "In function delete portfolio.";
		$sql = "DELETE FROM pflo_stk_info WHERE pflo_stk_info.pflo_id = $pflo_id";
		$result = $conn->query($sql);
        $sql = "DELETE FROM portfolio WHERE user_id = \"$user_id\" AND pflo_id = $pflo_id";
		$result = $conn->query($sql);
#		echo "<script>alert('Portfolio deleted!'); location.href=\"main.php\";</script>";
	}
	function transportfolio($pid,$tid,$user_id){
		// Transfer cash balance 
		$date = date("Y-m-d H:i:s");
		require "conn.php";
		echo "In function transfer portfolio";
		$sql = "SELECT * FROM portfolio WHERE user_id = $user_id AND pflo_id = $pid";
#		echo $sql;
        $result = $conn->query($sql);
		echo $conn->error;
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
              $cashBal1 = $row[cash_balance];
#			  echo $cashBal1;
            }
        }
		$sql = "SELECT portfolio.cash_balance FROM portfolio WHERE portfolio.pflo_id = $tid AND portfolio.user_id = $user_id";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
              $cashBal2 = $row[cash_balance];
#			  echo $cashBal2;
            }
        }
		$balance = $cashBal1 + $cashBal2;    
        $sql = "UPDATE portfolio SET cash_balance = $balance WHERE portfolio.pflo_id = $tid AND portfolio.user_id = $user_id";
        $result = $conn->query($sql);
		$conn->error;
		//Transfer stock
		$sql = "SELECT * FROM pflo_stk_info WHERE pflo_id = $pid";
		$result = $conn->query($sql);
        if($result->num_rows > 0) {
#			echo 'The row number is'.$result->num_rows;
            while($row = $result->fetch_assoc()){
                $stk_id = $row[stk_id];
#				echo 'Stock id='.$stk_id;
				$share = $row[share];
				$currency_id = $row[currency_id];
				$sql1 = "SELECT * FROM pflo_stk_info WHERE pflo_id = $tid AND stk_id = $stk_id";
				$result1 = $conn->query($sql1);
				if($result1->num_rows > 0) {
                    $row1 = $result1->fetch_assoc();
					$share1 = $row1[share];
					$share = $share + $share1;
#					echo 'Total share ='.$share;
					$sql2 = "UPDATE pflo_stk_info SET pflo_stk_info.share = $share WHERE pflo_stk_info.pflo_id = $tid AND pflo_stk_info.stk_id = $stk_id";
                    $result2 = $conn->query($sql2);
					echo $conn->error;
#					echo 'The row number is'.$result->num_rows;
					
				}else{
					$sql3 = "INSERT INTO pflo_stk_info (pflo_id, stk_id, share, currency_id) VALUES ('$tid', '$stk_id', '$share', '$currency_id')";
					$result3 = $conn->query($sql3);
					echo $conn->error;
#					echo 'The row number is'.$result->num_rows;
				}
				// Add transaction
/*				$sql4 = "SELECT stk_info.current_price, stk_info.idx_id FROM stk_info WHERE stk_info.stk_id = \"$stk_id\"";    
				$result4 = $conn->query($sql4);
				$row4 = $result4->fetch_assoc();
				$price = $row4[current_price];
				$sql5 = "SELECT currency.rate FROM currency WHERE currency.currency_id = $currency_id";   
				$result5 = $conn->query($sql5);
				$row5 = $result5->fetch_assoc();    
				$rate = $row5[rate];
				$total_value = $price * $rate * $share;
				$opt_type = "Transfer";
				$sql6 = "INSERT INTO pflo_transaction (pflo_id, stk_id, oprt_type, share, price, cash_value, date) VALUES ('$tid', '$stk_id', '$opt_type', '$share', '$price', '$total_value', \"$date\")"; 
				$result6 = $conn->query($sql6);
*/
				$sql7 = "UPDATE pflo_transaction SET pflo_transaction.pflo_id = $tid WHERE pflo_transaction.pflo_id = $pid AND pflo_transaction.stk_id = $stk_id";
                $result7 = $conn->query($sql7);
				echo $conn->error;

				$sql8 = "UPDATE pflo_trans_result SET pflo_trans_result.pflo_id = $tid WHERE pflo_trans_result.pflo_id = $pid AND pflo_trans_result.stk_id = $stk_id";
                $result8 = $conn->query($sql8);
				echo $conn->error;
				
			}
		}
		
	}
	
?>

<?php 
require "conn_close.php";
?>
