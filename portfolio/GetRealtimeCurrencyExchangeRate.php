<?php
$currency=array(
	'USD'=>array(
	    'name'=>'US Dollar',
	    'link'=>'http://finance.yahoo.com/d/quotes.csv?s=USDUSD=X&f=l1',
		'currency_id'=>'0',
	),
	'SGD'=>array(
	    'name'=>'Singapore Dollar',
	    'link'=>'http://finance.yahoo.com/d/quotes.csv?s=SGDUSD=X&f=l1',
		'currency_id'=>'1',
	),
	'INR'=>array(
	    'name'=>'Indian Rupee',
	    'link'=>'http://finance.yahoo.com/d/quotes.csv?s=INRUSD=X&f=l1',
		'currency_id'=>'2',
	),

);



#echo "Start<br>";
function update_currency($currency) {

	$servername = "sql.njit.edu";
    $username = "bx34";
    $password = "Xx0SnUuM";
    $dbname = "bx34";
	$table = "currency";
	
	$sql_connect = mysql_connect($servername,$username,$password) or die("SQL connect failed.</br>");
	$db_select = mysql_select_db($dbname) or die("Connect to db $dbname failed.<br>");
	$i=0;
	$query = "SELECT * FROM $table";
	$q_result = mysql_query($query,$sql_connect);
#	echo "Test the loop"; 
	if(!$q_result){
		die("Failed to query table $table.<br>");
	}

	foreach(array_keys($currency) as $symbol){
#        echo 'in the loop';
		$currency_name = $currency[$symbol]['name'];
		$currency_link = $stock[$symbol]['link'];
		$currency_id = $stock[$symbol]['currency_id'];
		
		/*		$query = "SELECT * FROM $table WHERE Symbol = '$symbol';";
		$q_result = mysql_query($query,$sql_connect);
		if(!$q_result){
			die("Connect to db failed.<br>");
		}
		$q_num = mysql_num_rows($q_result);
		$stock_name = $stock[$symbol]['name'];
		$idx_id = $stock[$symbol]['idx_id'];
		$stk_id = $stock[$symbol]['stk_id'];
		if($q_num < 1){
		$insert = "INSERT INTO $table (stk_id, idx_id, stk_name, symbol)
								 VALUES ('$stk_id', '$idx_id', '$stock_name', '$symbol');";
		}

*/
        $rate = file_get_contents($currency[$symbol]['link']);
		$currency_id = $currency[$symbol]['currency_id'];
		$update = "UPDATE $table SET rate=$rate WHERE currency_id = $currency_id;";
		$update_result = mysql_query($update,$sql_connect);
		if(!$update_result){
				echo $table." ".$symbol."The update failed.<br>";
		}
#        echo mysql_error();

   	}
		

}
#update_currency($currency);

update_currency($currency);


?>