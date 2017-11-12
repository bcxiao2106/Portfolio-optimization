<?php
  require("conn.php");
?>
<?php 
	//$sql = "SELECT * FROM portfolio_view_domestic WHERE pflo_id = \"$pflo_id\"";
	$pid = $_POST['pid'];
	$sql = "SELECT 
        stk_info.stk_id,
        stk_info.idx_id,
        pflo_stk_info.pflo_id,
        portfolio.cash_balance,
        stk_info.stk_name,
        stk_info.symbol,
        stk_info.current_price,
        pflo_stk_info.share,
        idx_info.idx_name,
        currency.currency_id,
        currency.currency_name,
        currency.rate
    FROM
       stk_info,pflo_stk_info,portfolio,currency,idx_info
    WHERE
        stk_info.stk_id = pflo_stk_info.stk_id
            AND pflo_stk_info.currency_id = currency.currency_id
            AND pflo_stk_info.pflo_id = portfolio.pflo_id
            AND stk_info.idx_id = idx_info.idx_id
            AND idx_info.idx_id = 0
			AND pflo_stk_info.pflo_id = $pid";
			
	$result = $conn->query($sql);
	define('stk_name', 'stk_name');
	define('symbol', 'symbol');
	define('share', 'share');
	define('last_price', 'last_price');
	define('idx_name', 'idx_name');
	define('currency_name', 'currency_name');
	define('rate', 'rate');
	define('currency', 'currency');
	define('cash_balance', 'cash_balance');
	define('current_price', 'current_price');
	
	$count = 1;
	$subtotal_dow = 0;
	$subtotal_percent_dow = 0;
	$portion = 0;
	$totalMktVal = 0;
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
		
		
		//$mkt_value = $row[share] * $row[last_price];
		$mkt_value = $row[share] * $row[current_price];
		$currency = $row[currency_name];
		$rate = $row[rate];
		$portion = $mkt_value / $row[cash_balance] * 100;  ////Wrong!
		$subtotal_percent_dow = $subtotal_percent_dow + $portion;
			echo "<tr>";
			echo "<td>$count</td>";
			echo "<td>$row[stk_name]</td>";
			echo "<td>$row[symbol]</td>";
			echo "<td>$row[idx_name]</td>";
			echo "<td>$row[share]</td>";
			//echo "<td>$row[last_price]</td>";
			echo "<td>$row[current_price]</td>";
			echo "<td>$mkt_value</td>";
			//echo "<td>$portion%</td>";
			echo "</tr>";
			$subtotal_dow = $subtotal_dow + $mkt_value;
			$count++;
		}
		echo "<tr>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td>Subtotal</td>";
		echo "<td>$subtotal_dow</td>";
		//echo "<td>$subtotal_percent_dow%</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td colspan=\"8\">You have no stock in this section.</td>";
		echo "</tr>";
	}
	echo "<script language=\"javascript\">currentItemNo = $count + 1</script>";
	require "conn_close.php";
?>