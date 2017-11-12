<?php
	set_include_path('/afs/cad/u/b/x/bx34/public_html/portfolio/ssh2/');
	include('/afs/cad/u/b/x/bx34/public_html/portfolio/ssh2/Net/SSH2.php');
	require "session_check.php";
	require "conn.php";
	$user_id = $_SESSION['user_id']; 
	$user_name = $_SESSION['user_name'];
	$pflo_id = $_GET['pid'];
	$tolerant_rsk = $_GET['rsk'];
	
	$w = array();
	$beta = array();
	$er = array();
	$i = 1;
	
	$sql = "SELECT * FROM pflo_stk_info WHERE pflo_id = $pflo_id";
	$result = $conn->query($sql);
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			$stk_id = $row[stk_id];
#				echo 'Stock id='.$stk_id;
			
			$sql1 = "SELECT * FROM stk_cal WHERE stk_id = $stk_id";
			$result1 = $conn->query($sql1);
			if($result1->num_rows > 0) {
				$row1 = $result1->fetch_assoc();
				$beta[$i] = $row1[Beta];
				$er[$i] = $row1[ER];

#				echo 'er is' . $er[$i] .' - '. $beta[$i].'<br>';
				
			}	
		$i++;
		}
	}
	
	$sql = "SELECT * FROM pflo_stk_info WHERE pflo_id = $pflo_id";
	$result = $conn->query($sql);
	$num_row = $result->num_rows;
#	echo 'The number of row is' . $num_row;
    
	$max = '';
	$weight = '  weight: ';
	$beta_string = ' beta: ';
	$bounds = array();
	$Bounds_sum = '';
	$low = 10;
	$high = 200;
	$int = '';
	for ($x=1; $x<= $num_row; $x++) {
		if( $x < $num_row){
			$max = $max . ' ' . $er[$x] . ' w' . $x . ' +';
#			echo $x;
			$weight = $weight . ' w' . $x . ' +';
			$beta_string = $beta_string . ' ' . $beta[$x] . ' w' . $x . ' +';
		} else{
			$max = $max . ' ' . $er[$x] . ' w' . $x;
			$weight = $weight . ' w' . $x . ' = 1000';
			$beta_string = $beta_string . $beta[$x] . ' w' . $x .  " \<= ".$tolerant_rsk;
		}
		$bounds[$x] = '  ' . $low . " \< " . 'w' . $x . " \<= " . $high;
		$int = $int . '  w' . $x;
	}

	$head = 'Maximize';
	$subject = 'Subject To';
	$bounds_string = 'Bounds';
	$int_string = 'Integers';
	$end = 'End';
	$out = shell_exec('echo \'\' >| optimizer.sol');
    $out = shell_exec("echo $head >| optimizer.lp");
	$out = shell_exec("echo $max >> optimizer.lp");
	$out = shell_exec("echo $subject >> optimizer.lp");
	$out = shell_exec("echo $weight >> optimizer.lp");
	$out = shell_exec("echo $beta_string >> optimizer.lp");
	$out = shell_exec("echo $bounds_string >> optimizer.lp");
	for($x=1; $x<= $num_row; $x++){
		$out = shell_exec("echo $bounds[$x] >> optimizer.lp");
	}
	$out = shell_exec("echo $int_string >> optimizer.lp");
	$out = shell_exec("echo $int >> optimizer.lp");
	$out = shell_exec("echo $end >> optimizer.lp");
	

// Below should be revised accordingly
	$ssh = new Net_SSH2('afsconnect1.njit.edu', 22); # this is the afs used, the same as gurobi is installed
	$ssh->login('bx34', 'China1234'); # The password should be your afs password
	$output = $ssh->exec('pwd');
	$output = $ssh->exec('gurobi_cl ResultFile=optimizer.sol public_html/portfolio/optimizer.lp');
#	echo $output;
	$output = $ssh->exec('cp optimizer.sol public_html/portfolio/optimizer.sol');
	$handle = fopen("optimizer.sol", "r");
	$i = 1;
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			// process the line read.
			if(preg_match("/w\d/",$line,$matches)){
				preg_match("/w\d (\d+)/",$line,$matches);
				$w[$i] = $matches[1] / 1000;
				$i++;
			}
		}

    fclose($handle);
	} else {
		echo 'error opening file optimizer.sol';
	} 

	$i = 1;
	$sql = "SELECT * FROM pflo_stk_info WHERE pflo_id = $pflo_id";
#	echo $sql;
	$result = $conn->query($sql);
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			$stk_id = $row[stk_id];
#				echo 'Stock id='.$stk_id;
			
			$sql1 = "UPDATE pflo_stk_info SET pflo_stk_info.weight = $w[$i] WHERE pflo_stk_info.pflo_id = $pflo_id AND pflo_stk_info.stk_id = $stk_id";
#			echo $sql1;
			$result1 = $conn->query($sql1);
#			echo $conn->error;
			$i++;
		}
	}
	$output = $ssh->disconnect();
	
	$sql = "SELECT * FROM portfolio WHERE pflo_id = $pflo_id";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$pflo_name = $row['pflo_name'];
			$pflo_balance = $row['cash_balance'];
		}
	}
#	echo "<br>".$pflo_balance."<br>";
	
#Get total market value of all stocks in the portfolio
	$sql = "SELECT SUM(SHARE*CURR_PRICE*RATE) as TOTAL_MKT_VALUE FROM 
				(SELECT PSI.share as SHARE, 
						SI.current_price as CURR_PRICE, 
						C.rate as RATE
						FROM pflo_stk_info PSI JOIN portfolio P ON PSI.pflo_id = P.pflo_id 
								JOIN stk_info SI ON PSI.stk_id = SI.stk_id 
								JOIN currency C ON SI.idx_id = C.currency_id
						WHERE PSI.pflo_id = $pflo_id) A";
#	echo $sql."<br>";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$tot_mktValue = $row['TOTAL_MKT_VALUE'];
		}
	}
#	echo $tot_mktValue."<br>";
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
	<script language="javascript">
	function reDirection(pflo_id){
		location.href = "main.php?pid="+pflo_id;
	}
	</script>
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
		<div class="container">		  
            <h4 class="sub-header"><?php echo $pflo_name;?></h4>
            <div class="table-responsive">
            <table  class="table table-striped" >
              <thead>
                <tr>
                  <th>#</th>
                  <th>Symbol</th>
				  <th>Index</th>
                  <th>Share</th>
                  <th>Current Price</th>
                  <th>Current Price(USD)</th>
				  <th>Market Value(USD)</th>
				  <th>Weight</th>
				  <th>Optimized Weight</th>
				  <th>Optimized Share</th>
                </tr>
              </thead>
              <tbody id="intStkInfo">
<?php
	$sql = "SELECT PSI.pflo_id,
					PSI.stk_id,
					PSI.share,
					PSI.weight,
					P.pflo_name,
					P.cash_balance,
					SI.stk_name,
					II.idx_name,
					SI.symbol,
					SI.current_price,
					C.currency_name,
					C.rate,PSI.share*SI.current_price*C.rate as mkt_value,
					SC.ER,
					SC.Beta
		FROM pflo_stk_info PSI JOIN portfolio P ON PSI.pflo_id = P.pflo_id 
			JOIN stk_info SI ON PSI.stk_id = SI.stk_id
            JOIN currency C ON SI.idx_id = C.currency_id
            JOIN idx_info II ON II.idx_id = SI.idx_id
            JOIN stk_cal SC ON SC.stk_id = PSI.stk_id
            WHERE PSI.pflo_id =  $pflo_id";
#			echo $sql."<br>";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		$i = 1;
		$WER = 0;
		$WER_OPT = 0;
		$WBT = 0;
		$WBT_OPT = 0;
		while($row = $result->fetch_assoc()){
			$optm_share = ceil($row['weight']*$tot_mktValue/$row['rate']/$row['current_price']);
			$WER = $WER + $row['mkt_value']/$tot_mktValue*$row['ER'];
			$WER_OPT = $WER_OPT + $row['weight']*$row['ER'];
			$WBT = $WBT + $row['mkt_value']/$tot_mktValue*$row['Beta'];
			$WBT_OPT = $WBT_OPT + $row['weight']*$row['Beta'];
?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $row['symbol'];?></td>
			<td><?php echo $row['idx_name'];?></td>
			<td><?php echo $row['share'];?></td>
			<td><?php echo $row['current_price'];?></td>
			<td><?php echo round($row['current_price'] * $row['rate'],2);?></td>
			<td><?php echo round($row['mkt_value'],2);?></td>
			<td><?php echo round($row['mkt_value']/$tot_mktValue*100,1)."%"; ?></td>
			<td><?php echo round($row['weight']*100,2)."%";?></td>
			<td <?php if($optm_share<$row['share']) echo "bgcolor=\"#FF6600\""; if($optm_share>$row['share'])  echo "bgcolor=\"#33CC99\"";?>><?php echo $optm_share;?></td>
		</tr>
<?php
	$i++;
		}
	}

?>
              </tbody>
            </table>
			<hr>
			<p>- Original Expect Return : <?php echo round($WER,2);?></p>
			<p>- Expect Return After Optimization: <?php echo round($WER_OPT,2);?></p>
			<p>- Original Beta : <?php echo round($WBT,2);?></p>
			<p>- Beta After Optimization: <?php echo round($WBT_OPT,2);?></p>
			<p>- Current Market Value : <?php echo round($tot_mktValue,2);?></p>
			<p>- Current Cash Balance : <?php echo $pflo_balance;?></p>
			<button type="submit" class="btn btn-success" name="back" onClick="return reDirection(<?php echo $pflo_id; ?>)">Back</button>
          </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./js/jquery.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="./js/bootstrap.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="./js/holder.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  
</body></html>
<?php
require "conn_close.php";
?>
