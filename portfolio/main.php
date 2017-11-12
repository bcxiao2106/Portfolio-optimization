<?php
require "session_check.php";
require "conn.php";
#require "GetRealtime.php";

	// check user_id and porfolio_id
	$user_id = $_SESSION['user_id'];
	$user_name = $_SESSION['user_name'];
	define('pflo_name', 'pflo_name');
	define('pflo_id', 'pflo_id');
	define('pid', 'pid');
	
	if($_GET['pid'] == null || $_GET['pid'] == ""){
	
	
	//load the first portfolio created by user
		$sql = "SELECT * FROM portfolio WHERE user_id = $user_id ORDER BY pflo_id LIMIT 1";
		//echo $sql;
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$pflo_name = $row['pflo_name'];
				$pflo_id = $row['pflo_id'];
			}
		} else {//no portfolio found & illegal acces to this page
			echo "<script language=\"javascript\">";
			echo "location.href=\"index.html\";";
			echo"</script>";
		}
	} else {//load current portfolio
		$pflo_id = $_GET['pid'];
		$sql = "SELECT * FROM portfolio WHERE user_id = \"$user_id\" AND pflo_id = $pflo_id";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$pflo_name = $row['pflo_name'];
			}
		}
	}
?>
<script language="javascript">
function startOptmiz(pfloId){
	var rskSeltdValue;
	var rskArray = document.getElementsByName("rsk");
	for (var i = 0; i < rskArray.length; i++) {
		if (rskArray[i].checked == true) {
			rskSeltdValue = rskArray[i].value;
        }
    }
	location.href = "OptimizePortfolio.php?pid=" + pfloId + "&rsk=" + rskSeltdValue;
	return false;
}
</script>
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
				echo "<li class=\"active\"><a href=main.php?pid=$row[pflo_id]>-&nbsp;$row[pflo_name]<span class=\"sr-only\">(current)</span></a></li>";
			} else {
				echo "<li><a href=main.php?pid=$row[pflo_id]>-&nbsp;$row[pflo_name]</a></li>";
			}
			
		}
	}
?>
          </ul>
          <ul class="nav nav-sidebar">
			<li><a href="startup_wizard.php">Create New Portfolio</a></li>
			<li><a href="OptimizePortfolio.php?pid=<?php echo $pflo_id ?>">Optimize Portfolio</a></li>
            <li><a href="modify_portfolio.php?pid=<?php echo $pflo_id ?>">Edit Portfolio</a></li>
            <li><a href="DelPortfolio.php?pid=<?php echo $pflo_id ?>">Delete Portfolio</a></li>
			<li><a href="exportToCSV.php?pid=<?php echo $pflo_id; ?>">Export Portfolio</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="">My Information</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <ul class="nav nav-tabs">
        <li class="active">
          <a href="#1" data-toggle="tab" onClick="getDomStk(<?php echo $pflo_id; ?>); getIntStk(<?php echo $pflo_id; ?>);">Overview</a>
        </li>
        <li>
          <a href="#2" data-toggle="tab" onClick="portfolioValidation(<?php echo $pflo_id; ?>)">Performance</a>
        </li>
        <li>
          <a href="#3" data-toggle="tab">Optimization</a>
        </li>
        <li>
          <a href="#4" data-toggle="tab">Last Optimization</a>
        </li>
        <li>
          <a href="#5" data-toggle="tab" onClick="portfolioTrans(<?php echo $pflo_id; ?>)">Transaction</a>
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
              <tbody id="domStkInfo">
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
              <tbody id="intStkInfo">

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
          <div class="container" id="optimization">
		  <h4>Risk tolerance: </h4>
		  <form method="post" action="OptimizePortfolio.php" name="optimzFrm">
				<p><input type="radio" name="rsk" id="rsk" value="1270" checked>Low</p>
				<p><input type="radio" name="rsk" id="rsk" value="1300">Medium</p>
				<p><input type="radio" name="rsk" id="rsk" value="1350">High</p>
			  <button type="submit" class="btn btn-success" name="optimizationBtn" onClick="return startOptmiz(<?php echo $pflo_id; ?>)">Start Optimization</button>
		  </form>
		  </div>
        </div>
      <div class="tab-pane" id="4">
          <div class="container" id="lastOptimization">
<?php 
  $sql = "SELECT * FROM portfolio WHERE pflo_id = $pflo_id";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $pflo_name = $row['pflo_name'];
      $pflo_balance = $row['cash_balance'];
    }
  }
# echo "<br>".$pflo_balance."<br>";
  
#Get total market value of all stocks in the portfolio
  $sql = "SELECT SUM(SHARE*CURR_PRICE*RATE) as TOTAL_MKT_VALUE FROM 
        (SELECT PSI.share as SHARE, 
            SI.current_price as CURR_PRICE, 
            C.rate as RATE
            FROM pflo_stk_info PSI JOIN portfolio P ON PSI.pflo_id = P.pflo_id 
                JOIN stk_info SI ON PSI.stk_id = SI.stk_id 
                JOIN currency C ON SI.idx_id = C.currency_id
            WHERE PSI.pflo_id = $pflo_id) A";
# echo $sql."<br>";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      $tot_mktValue = $row['TOTAL_MKT_VALUE'];
    }
  }
?>
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
#     echo $sql."<br>";
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
          </div>
      </div>
        </div>
        <div class="tab-pane" id="5">
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
          		
		  
<!--Add Transaction start-->		  
		  <hr>
		  <div class="container">
          <h4 class="sub-header">Add transaction</h4>
          <div class="table-responsive">
           <form class="form-group" name="newTransForm" method="post"> <!-- action="SellBuy.php"-->
            <table class="table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Symbol</th>
                  <th>Date</th>
                  <th>Current Price</th>
                  <th>Share</th>
				  <th></th>
                </tr>
              </thead>
              <tbody>
			 
			    <tr>
                  <td><select class="form-control" name ="transType" id="transType">
						<option value="Buy" id="stkBuy">Buy</option>
						<option value="Sell" id="stkSell">Sell</option>
					  </select></td>
                  <td><input class="form-control" type="text" list="json-datalist" id="stkSymbol" name="symbol" placeholder = "e.g. IBM"></input></td>
                  <datalist id="json-datalist"></datalist>
                  <td><input class="form-control" value="<?php echo date("m/d/Y"); ?>" disabled></input></td>
                  <td><input class="form-control" id="stkPrice"></input></td>
                  <td><input class="form-control" type="number" name="sShare" id="stkShare"></input></td>                  
				  <td><input type="hidden" name="pid" value="<?php echo $pflo_id; ?>"></td>
				  <td><input type="hidden" name="uid" value="<?php echo $user_id; ?>"></td>
				  <td><input type="hidden" name="sDate" value="<?php echo date("m/d/Y"); ?>"></td>
                </tr>                
              </tbody>
            </table>
            <!--add sell table here-->
            <div id="sellTable" style="display:none">
                <table class="table table-striped" id="addSellTable">
                <thead>
                <tr>
                  <th>Symbol</th>
                  <th>Purchase Date</th>
                  <th>Purchase Price</th>
                  <th>Share</th>
                  <th>Sell</th>
                </tr>
                </thead>
                <tbody id="sellTableBody">
                </tbody>
                </table>
                </div>
            <button type="submit" class="btn btn-success" name="sub" onclick="getTableData()">Submit</button>
            </form>
          </div>
          </div>
<!--Add Transaction end-->
<!-- Deposite/Withdraw container -->
        <div class="container">
          <h4 class="sub-header">Deposit/Withdraw</h4>
          <div class="table-responsive">
            <table class="table-condensed">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Date</th>
                  <th>Amount</th>
				  <th></th>
                </tr>
              </thead>
              <tbody>
          <form action="DepWit.php" method="post" name="CashForm">
            <tr><td>
              <select class="form-control" name="cashType">
                <option value="Deposit">Deposit</option>
                <option value="Withdraw">Withdraw</option>
              </select></td>
              <td><input class="form-control" type="text" value="<?php echo date("m/d/Y"); ?>" disabled></input></td>
            <td>
              <input class="form-control" type="number" name="cashNumber"></input>
            </td>
             <td><input type="hidden" name="pid" value="<?php echo $pflo_id; ?>"></td>
			 <td><input type="hidden" name="uid" value="<?php echo $user_id; ?>"></td>
			 <td><input type="hidden" name="cashDate" value="<?php echo date("m/d/Y"); ?>"></td>
          </tr>
          <tr><td colspan="4">
          <button type="submit" name="sub1" class="btn btn-success">Submit</button></td></tr>
        </form>
        </tbody>
        </table>
        </div><!-- Deposite/Withdraw container ends here -->     </div>
      <hr>



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
  
 <script>
      var datalist = document.getElementById('json-datalist');
      var input = document.getElementById('stkSymbol');
      var request = new XMLHttpRequest();
      
      request.onreadystatechange = function(response) {
        if (request.readyState == 4) {
          if (request.status == 200) {
            var options = JSON.parse(request.responseText);
            var n = 0;
            options.forEach(function(item) {
              var option = document.createElement('option');
              option.value = item;
              option.id = n;
              datalist.appendChild(option);
              n++;
            });
            input.placeholder = "e.g. IBM";
          } else {
            input.placeholder = "Couldn't load options";
          }
        }
      };
      input.placeholder = "Loading options...";
      request.open('GET', 'stockSymbol.json', true);
      request.send();
    </script>
    <script>
    $(document).ready(function() {
      var pid = <?php echo $pflo_id; ?>;
      
      $('#transType').on('change', function(e){
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        if(valueSelected == "Buy") {
           $("#sellTable").hide();
           $("#stkShare").prop('disabled', false);
         }
        else { 
          $("#stkShare").prop('disabled', true);
          }
          
      });
      
      $.ajax ({
        type: 'POST',
        url: 'getStkInfo.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#domStkInfo').html(data);
        }
      });
      $.ajax ({
        type: 'POST',
        url: 'getIntInfo.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#intStkInfo').html(data);
        }
      });
      $('#stkSymbol').change(function() {
        var stk = $('#stkSymbol').val();
        $.ajax({
          type: 'POST',
          url: 'getCurrentPrice.php',
          data: 'stk='+stk,
          success: function(data) {
            $('#stkPrice').val(data);
          }
         });
         if($("#transType").val() == "Sell") {
           $.ajax ({
              type: 'POST',
              url: 'getSellTable.php',
              data: 'pid='+pid+'&symbol='+stk,
              success: function(data) {
                $('#sellTableBody').html(data);
                console.log(stk);
              }
            });
            $("#sellTable").show();
         }
      });
    });
    
    function getTableData() {
      var table = document.getElementById("sellTableBody");
      
      var pid = document.getElementsByName("pid")[0].value;
      var uid = document.getElementsByName("uid")[0].value;
      var sShare = document.getElementsByName("sShare")[0].value;
      var transType = $( "#transType option:selected" ).text();
      var symbol = document.getElementsByName("symbol")[0].value;
      
      var stkCurPrice = $('#stkPrice').val();
      var value1 = 0;
      var value2 = 0;
      var dataArray = {};
      if(sShare == "" && transType == "Sell"){
        sShare = "n";
      }
      dataArray['pid'] = pid;
      dataArray['uid'] = uid;
      dataArray['sShare'] = sShare;
      dataArray['transType'] = transType;
      dataArray['symbol'] = symbol;
      for (var i = 0, row; row = table.rows[i]; i++) {
        dataArray['date'+i] = row.cells[1].innerHTML;
        dataArray['price'+i] = row.cells[2].innerHTML;
        dataArray['share'+i] = row.cells[4].children[0].value;
        value2 = value2 + row.cells[2].innerHTML * row.cells[4].children[0].value;
        value1 = value1 + row.cells[4].children[0].value * stkCurPrice;
      }
      
      dataArray = JSON.stringify(dataArray);
      //var dataArray = { pid: pid, uid: uid, sShare: sShare, transType: transType, symbol: symbol};
      if(sShare == "n" && transType == "Sell" && value2 != 0){
        //console.log(sShare);
        if(value1 > value2) {   //gain profit
          var answer = confirm("Please note that tax(15% - 20%) will be added on your gain. Are you still willing to sell?");
          if(answer) {
            
      $.ajax ({
        type: 'POST',
        url: 'SellBuy.php',
        //dataType:'JSON',
        //data: "dataArray="+dataArray+"&pid="+pid+"&uid="+uid+"&sShare="+sShare+"&transType="+transType+"&symbol="+symbol,
        data:{dataArray:dataArray},
        success: function(data) {
          alert(data);
          alert("To keep your portfolio balanced please use optimizer to buy stocks.");
          /*
          portfolioValidation(pid);
          //window.location.href ='main.php?pid='+pid
          $( "#stkTab" ).removeClass("active");
          $("#1").removeClass("tab-pane active" ).addClass( "tab-pane" );
          
          $("#valTab").addClass("active");
          $("#2").addClass(" active");*/
        }
      });
          } else {
            return false;
          }
        } else { //lose money
          $.ajax ({
        type: 'POST',
        url: 'SellBuy.php',
        data:{dataArray:dataArray},
        success: function(data) {
          //console.log("sss");
          //console.log(data);
          alert(data);
          alert("To keep your portfolio balanced please use optimizer to buy stocks.");
          //window.location.href ='main.php?pid='+pid
          /*
          portfolioValidation(pid);
          //window.location.href ='main.php?pid='+pid
          $( "#stkTab" ).removeClass("active");
          $("#1").removeClass("tab-pane active" ).addClass( "tab-pane" );
          
          $("#valTab").addClass("active");
          $("#2").addClass(" active");*/
        }
      });
        }
      } else if(transType == "Buy") {
        $.ajax ({
        type: 'POST',
        url: 'SellBuy.php',
        data:{dataArray:dataArray},
        success: function(data) {
          alert(data);
          //window.location.href ='main.php?pid='+pid
        }
      });
      }
      
      
      //console.log( JSON.stringify(dataArray));
      
    }
    function portfolioValidation(pid) {
      $.ajax ({
        type: 'POST',
        url: 'view.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#viewValidation').html(data);
        }
      });
    }
    
    function portfolioTrans(pid) {
      $.ajax ({
        type: 'POST',
        url: 'getTransData.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#transTab').html(data);
        }
      });
    }
    
    function getDomStk(pid) { 
      $.ajax ({
        type: 'POST',
        url: 'getStkInfo.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#domStkInfo').html(data);
        }
      });
    }
    function getIntStk(pid) { 
      $.ajax ({
        type: 'POST',
        url: 'getIntInfo.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#intStkInfo').html(data);
        }
      });
    }
      var pid = <?php echo $pflo_id; ?>;
      var myVar = setInterval(function () 
{ 
     $.ajax ({
        type: 'POST',
        url: 'getIntInfo.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#intStkInfo').html(data);
        }
      });
//   window.alert("Running real time.");
	$.ajax ({
        type: 'POST',
        url: 'getStkInfo.php',
        data: 'pid='+pid,
        success: function(data) {
          $('#domStkInfo').html(data);
        }
      });
},180000);
    </script>

<!-- Below is code for realtime update stock price and currency exchange. -->

    <script>


var run = setInterval(function () 
{
    $.ajax(
        {
               type: 'POST',
               url: 'GetRealtimeStockPrice.php',
         });
//   window.alert("Running real time.");
	$.ajax(
        {
               type: 'POST',
               url: 'GetRealtimeCurrencyExchangeRate.php',
         }); 
},120000);
	
    </script>	
</body></html>
<?php
require "conn_close.php";
?>