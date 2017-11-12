<?php
  require("conn.php");
  require "session_check.php";
  $user_id = $_SESSION['user_id'];
?>

<?php
	$date = date("Y-m-d H-i-s");
	$pflo_id = $_GET['pid'];
	$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\" AND pflo_id = $pflo_id";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$pflo_name = $row['pflo_name'];
		}
	}


    //indicate that the file is downloaded rather than displayed
    $file_name = 'Portfolio_'.$pflo_name.'_'.$date.'_data';
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$file_name");
  
    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // output the column headings

#	 fputcsv($output, array('Portfolio Name:', $pflo_name));
	fputcsv($output, array('Current holding stocks'));
	fputcsv($output, array(''));
    fputcsv($output, array('Stock_Name', 'Symbol', 'Currency', 'Purchase Price','Purchase Price(USD)', 'Current Price', 
                           'Current Price(USD)', 'Shares', 'Cash Value', 'Cash Value(USD)','Date'));

    $pid = $_GET['pid'];
    $sql = "SELECT
      stk_info.stk_name, stk_info.symbol, stk_info.current_price, idx_info.idx_name, currency.currency_name,currency.rate,  
      pflo_trans_result.pur_price, pflo_trans_result.share, pflo_trans_result.date
      FROM
      pflo_trans_result, stk_info, idx_info, currency
      WHERE
      pflo_trans_result.pflo_id = $pid
      AND stk_info.stk_id = pflo_trans_result.stk_id
	  AND idx_info.idx_id = stk_info.idx_id
	  AND currency.currency_id = idx_info.idx_id";        
    $result = $conn->query($sql);
    $total_value = 0;
    if($result->num_rows > 0){
	    while($row = $result->fetch_assoc()){
			$pur_price = $row[pur_price] * $row[rate];
			$cur_price = $row[current_price] * $row[rate];
			$cash_value = $row[current_price] * $row[share];
			$cash_value_usd = $cur_price * $row[share];
		    $total_value = $total_value + $cash_value_usd;
			$line = array($row[stk_name], $row[symbol], $row[currency_name], $row[pur_price], $pur_price, $row[current_price],
                          $cur_price, $row[share], $cash_value, $cash_value_usd, $row['date']);
		    fputcsv($output, $line);
	    } 
	} 
	fputcsv($output, array(''));
	fputcsv($output, array('Total Value:',$total_value));
	$sql = "SELECT * FROM portfolio WHERE user_id = $user_id AND pflo_id = $pid";
#		echo $sql;
	$result = $conn->query($sql);
#	echo $conn->error;
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
		  $cashBal = $row[cash_balance];
#			  echo $cashBal1;
		}
	}
#	fputcsv($output, array(''));
	fputcsv($output, array('Cash(USD):', $cashBal));
	$net_value = $total_value + $cashBal;
    fputcsv($output, array('Net Asset Value:', $net_value));

	
	fputcsv($output, array(''));
	fputcsv($output, array(''));
	fputcsv($output, array('Transaction History'));
#	Stock_Name	Symbol	Type	Currency	Purchase_Price	Purchase_Price(USD)	Sell_Price	Sell_Price(USD)	Shares	Cash_Value	Cash_Value(USD)	Date
    fputcsv($output, array('Stock_Name', 'Symbol', 'Type', 'Currency', 'Price','Price(USD)',  
                           'Shares', 'Cash Value', 'Cash Value(USD)','Date'));

      
    $sql = "SELECT
      stk_info.stk_name, stk_info.symbol, pflo_transaction.oprt_type, idx_info.idx_name, currency.currency_name,currency.rate, 
      pflo_transaction.price, pflo_transaction.share, pflo_transaction.cash_value, pflo_transaction.date
      FROM
      pflo_transaction, stk_info, idx_info, currency
      WHERE
      pflo_transaction.pflo_id = $pid
      AND stk_info.stk_id = pflo_transaction.stk_id
	  AND idx_info.idx_id = stk_info.idx_id
	  AND currency.currency_id = idx_info.idx_id";        

	$result = $conn->query($sql);
    
    if($result->num_rows > 0){
	    while($row = $result->fetch_assoc()){
			$price = $row[price] / $row[rate];
			$cash_value = $row[cash_value] / $row[rate];
			$line = array($row[stk_name], $row[symbol], $row[oprt_type], $row[currency_name], $price, $row[price],
                          $row[share], $cash_value, $row[cash_value], $row['date']);
			fputcsv($output, $line);
		} 
	} 

	fclose($output);
	
	exit;
	
	require "conn_close.php";
?>