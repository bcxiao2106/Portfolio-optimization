<?php
  require("conn.php");
?>

<?php
  if(isset($_POST['pid'])) {
  
      $pid = $_POST['pid'];
      
      $sql = "SELECT
      pflo_stk_info.stk_id, pflo_stk_info.share, pflo_stk_info.currency_id,
      stk_info.current_price, currency.rate, portfolio.cash_balance
      FROM
      pflo_stk_info, currency, stk_info, portfolio
      WHERE
      pflo_stk_info.pflo_id = $pid
      AND stk_info.stk_id = pflo_stk_info.stk_id
      AND portfolio.pflo_id = $pid
      AND pflo_stk_info.currency_id = currency.currency_id";
        
        $result = $conn->query($sql);
        // Find the total stock market value
        $stkTotalMkt = 0;
        $stkDeTotalMkt = 0;
        $stkInTotalMkt = 0;
        $count = 0;
        $totalMkt = 0;
        $cashPer = 0;
        $stkDePer = 0;
        $stkInPer = 0;

        if($result->num_rows > 0){
		  while($row = $result->fetch_assoc()){
		    $stkTotalMkt = $stkTotalMkt + $row[current_price]*$row[share]*$row[rate];
		    if ($row[currency_id] == 0) {  //USD
		      $stkDeTotalMkt = $stkDeTotalMkt + $row[current_price]*$row[share]*$row[rate];
		    } else { //SGD + INR
		      $stkInTotalMkt = $stkInTotalMkt + $row[current_price]*$row[share]*$row[rate];
		    }
		    $cashBalance = $row[cash_balance];
		    $count++;
		  }
		  //validate the number of stocks
          if ($count > 10 || $count < 7) {
            echo "<p>You have $count stocks in your portfolio. We suggest maintain the number between 7 to 10!</p>";
            
          } else {
            echo "<p>You have $count stocks in your portfolio.</p>";
          }
        
          $totalMkt = $stkTotalMkt + $cashBalance;
	      $cashPer = ($cashBalance/$totalMkt)*100;
	      $stkDePer = ($stkDeTotalMkt/$stkTotalMkt)*100;
	      $stkInPer = ($stkInTotalMkt/$stkTotalMkt)*100;
	      
        }  //the end of function
                          			  
	  
	  //validate the 70% 30%
	  
      echo "<p>Dow-30 stocks take $stkDePer% of your portfolio.</p>";
      echo "<p>International stocks take $stkInPer% of your portfolio.</p>";
      echo "<p>Cash takes $cashPer% of your portfolio.</p>";
	  
            
  }
  
  require "conn_close.php";
  
?>