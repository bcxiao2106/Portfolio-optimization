<?php
require "session_check.php";
require "conn.php";
	$user_id = $_SESSION['user_id'];
	$user_name = $_SESSION['user_name'];
    echo "start";
	$pflo_id = $_GET['pid'];
	$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\" AND pflo_id = $pflo_id";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$pflo_name = $row['pflo_name'];
	echo 'start';
?>

<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://getbootstrap.com/favicon.ico">

    <title>Portfolio Optimization System</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>


    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Portfolio Optimization System</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
			<li><a href="#">About POS</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#"><?php echo $user_name;?></a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
   <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#">My Portfolio: <span class="sr-only">(current)</span></a></li>
<?php 
	$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\"";
	$result = $conn->query($sql);
	if($result->num_rows >0){
		while($row = $result->fetch_assoc()){
			if($pflo_id == $row[pflo_id]){
				echo "<li class=\"active\"><a href=\"\">-&nbsp;$row[pflo_name]<span class=\"sr-only\">(current)</span></a></li>";
			} else {
				echo "<li><a href=\"\">-&nbsp;$row[pflo_name]</a></li>";
			}
			
		}
	}
?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		<ul class="nav nav-tabs">
        <li class="active">
          <a href="#1" data-toggle="tab">Overview</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="1">
          <div class="container">
          <!--Domestic Index Overview start-->
            <div class="container">
          <h4 class="sub-header"><?php echo $pflo_name;?> - Domestic</h4>
          <div class="table-responsive">
            <table  class="table table-striped" >
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Symbol</th>
				  <th>Index</th>
                  <th>Share</th>
                  <!--<th>Last Price</th>-->
                  <th>Current Price</th>
				  <th>Market Value</th>
				  <!--<th>%</th>-->
                </tr>
              </thead> 
              <tbody>
<?php 
	//$sql = "SELECT * FROM portfolio_view_domestic WHERE pflo_id = \"$pflo_id\"";
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
			AND pflo_stk_info.pflo_id = \"$pflo_id\"";
			
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
?>
              </tbody>
            </table>
          </div>
        </div>
            <!--Domestic Index Overview end-->		  

            <!--Overseas Index Overview start-->
            <div class="container">		  
            <h4 class="sub-header"><?php echo $pflo_name;?> - Overseas</h4>
            <div class="table-responsive">
            <table  class="table table-striped" >
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Symbol</th>
				  <th>Index</th>
                  <th>Share</th>
                  <!--<th>Last Price</th>-->
                  <th>Current Price</th>
                  <th>Current Price(USD)</th>
				  <th>Rate</th>
				  <th>Market Value(USD)</th>
				  <!--<th>%</th>-->
                </tr>
              </thead>
              <tbody>
<?php 
	//$sql = "SELECT * FROM portfolio_view_oversea WHERE pflo_id = \"$pflo_id\"";
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
            AND idx_info.idx_id <> 0
			AND pflo_stk_info.pflo_id = \"$pflo_id\"";
			
	$result = $conn->query($sql);
	//$count = 1;
	$subtotal_dow = 0;
	$subtotal_percent_dow = 0;
	$portion = 0;
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
		//$mkt_value = $row[share] * $row[last_price];
		$currency = $row[currency_name];
		$rate = $row[rate];
		$portion = $mkt_value / $row[cash_balance] * 100;
		$subtotal_percent_dow = $subtotal_percent_dow + $portion;
		$priceCurr = $rate * $row[current_price];
		$mkt_value = $row[share] * $row[current_price] * $rate;
			echo "<tr>";
			echo "<td>$count</td>";
			echo "<td>$row[stk_name]</td>";
			echo "<td>$row[symbol]</td>";
			echo "<td>$row[idx_name]</td>";
			echo "<td>$row[share]</td>";
			//echo "<td>$row[last_price]</td>";
			echo "<td>$row[current_price]</td>";
			echo "<td>$priceCurr</td>";
			echo "<td>$row[rate]</td>";
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
		echo "<td></td>";
		echo "<td></td>";
		echo "<td>Subtotal</td>";
		echo "<td>$subtotal_dow</td>";
		//echo "<td>$subtotal_percent_dow%</td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td colspan=\"9\">You have no stock in this section.</td>";
		echo "</tr>";
	}
	echo "<script language=\"javascript\">currentItemNo = $count + 1</script>";
?>
              </tbody>
            </table>
          </div>
          </div>
            <!--Overseas Index Overview end-->
            
            <!--Cash Overview start-->
            <div class="container">		  
            <h4 class="sub-header"><?php echo $pflo_name;?> - Cash</h4>
            <div class="table-responsive">
            <table  class="table table-striped" >
              <thead>
                <tr>
                  <th>Cash(USD)</th>
                </tr>
              </thead>
              <tbody>
<?php 
	//$sql = "SELECT * FROM portfolio_view_oversea WHERE pflo_id = \"$pflo_id\"";
	$sql = "SELECT      
        portfolio.cash_balance
    FROM
       portfolio
    WHERE
			portfolio.pflo_id = \"$pflo_id\"";
			
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
		$cash = $row[cash_balance];
			echo "<tr>";
			echo "<td>$cash</td>";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td></td>";
		echo "</tr>";
	} else {
		echo "<tr>";
		echo "<td colspan=\"9\">Cash balance is not available yet.</td>";
		echo "</tr>";
	}
	echo "<script language=\"javascript\">currentItemNo = $count + 1</script>";
?>
              </tbody>
            </table>
          </div>
          </div>
            <!--Cash Overview end-->
          </div>
        </div>
        <div class="tab-pane"  id="2">
          <div class="container" id="viewValidation"></div>
        </div>
        <div class="tab-pane" id="3">
          <div class="container"></div>
        </div>
        <div class="tab-pane" id="4">
          <div class="container"><!--Transaction Overview start-->          		  
            <h4 class="sub-header"><?php echo $pflo_name;?> - Transactions</h4>
            <div class="table-responsive" style="height:500px;">
            <table  class="table table-striped" >
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Symbol</th>
                  <th>Type</th>
				  <th>Date</th>
                  <th>Shares</th>
                  <th>Price(USD)</th>
                  <th>Cash Value(USD)</th>
                </tr>
              </thead>
              <tbody id="transTab">
              </tbody>
            </table>
            </div>
          </div><!--Transaction Overview Ends-->
        </div>
      </div>
          		
		  




        </div>
      </div>
    </div>
  

  
  <?php
	
	//Check how many portfolios the user has and if there are stocks left in the portfolio
	$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\"";
	$result = $conn->query($sql);
	
	if($result->num_rows == 1){
		$sql = "SELECT * FROM pflo_stk_info WHERE pflo_id = $pflo_id";
		$result = $conn->query($sql);
		$num_row = $result->num_rows;		
		$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\" AND pflo_id = $pflo_id";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$cash = $row[cash_balance];
		if($num_row > 0 || $cash >1){
			echo "<script language=\"javascript\">";
			echo "window.alert(\"Please sell all stocks and withdraw all cash in the portfolio before deletion.\");";
			echo "location.href=\"main.php\";";
			echo "</script>";	
		} else{
			echo "<script language=\"javascript\">";
			echo "location.href=\"DelPortfolioDatabase.php?pid=$pflo_id\";";
			echo "</script>";	
			
		}
		
		
	}else{
		$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\" AND pflo_id <> $pflo_id";
		$result = $conn->query($sql);
		echo "<div data-role=\"popup\" id=\"myPopup\" class=\"ui-content\" style=\"border: 1px solid;background-color:lightblue;z-index:3; position: absolute;top: 100px;margin-left:350px; min-width:800px;text-align:center;shadow: 10px 10px 5px;\">";
		echo "  <form method=\"post\" action=\"DelPortfolioDatabase.php?pid=$pflo_id\">";
		echo "    <div>";
		echo "      <h4>Delete Portfolio $pflo_name: Please select the portfolio name you want to transfer stocks to</h4>";
		echo "      <select name=\"selectportfolio\" style=\"width:200px;\">";
		while($row = $result->fetch_assoc()){
			$p_name = $row[pflo_name];
			$p_id = $row[pflo_id];
			echo "          <option value=\"$p_id\">$p_name</option>";
		}
		echo "          <input type=\"submit\" data-inline=\"true\" value=\"Submit\">";
		echo "      </select>";
		echo "    </div>";
		echo "  </form>";
		echo "</div>";		

	}

?>

<script language="javascript">

	function confirmDelPflo(delname,transname){
		window.alert(5 + 6);
		if(confirm("Do you want to delete "+delname))
		{
			location.href="main.php";
		}else{
			url = "DelPortfolioDatabase.php?dname=" + delname + "&tname=" + transname;
			location.href=url;
		}	
    }

	function inputPfloName(dname,namestring,pid){
        
		window.alert(5 + 6);
		var transname = prompt("Please input portfolio name to be transferred:");
		if(namestring.indexOf(transname) !== -1){
			confirmDelPflo(dname,transname);
		}else{
			url = "DelPortfolio.php?pid=" + pid;
			location.href=url;
		}
		//		<?php echo delPflo(p_name);?>;
    }
	
 
</script>

</body></html>
<?php 
require "conn_close.php";
?>