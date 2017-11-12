<?php
	require "session_check.php";
	require "conn.php";

	define("pflo_name", "pflo_name");
	define("pflo_id", "pflo_id");
	define("pid", "pid");
	define("stk_name", "stk_name");
	define("symbol", "symbol");
	define("share", "share");
	define("last_price", "last_price");
	define("idx_name", "idx_name");
	define("currency_name", "currency_name");
	define("rate", "rate");
	define("currency", "currency");
	define("cash_balance", "cash_balance");
	define("error_msg_1", "error_msg_1");
	define("error_msg_2", "error_msg_2");
	define("newDeposit", "newDeposit");

// check user_id and porfolio_id
	$user_id = $_SESSION['user_id'];
	$user_name = $_SESSION['user_name'];
	$error_msg_1 = "";
	$error_msg_2 = "";
	
		if($_POST["pflo_name"] != null || $_POST["pflo_name"] != "" || $_POST["newDeposit"] != null || $_POST["newDeposit"] != ""){
			$pflo_name = $_POST["pflo_name"];
			$newDeposit = $_POST["newDeposit"];
			//Query for existing portfolio name
			$sql = "SELECT pflo_name FROM portfolio WHERE pflo_name = \"$pflo_name\" AND user_id = $user_id";
			$result = $conn->query($sql);
			
			if($result->num_rows > 0){//existing name
				$error_msg_1 = "Name occupied!";
			} else {//creat new portfolio
				$sql = "INSERT INTO portfolio (pflo_name,user_id,cash_balance) VALUES (\"$pflo_name\", $user_id, $newDeposit)";
				if ($conn->query($sql) === TRUE) {
					echo "<script language=\"javascript\">";
					//echo "alert(\"Portfolio created successfully\");";
					echo"</script>";
					$error_msg_1 = "success";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}
		}
?>

<!DOCTYPE html>
<html lang="en">
<style>
  body {
      position: relative; 
  }
  #section1 {padding-top:30px;height:500px;color: #333333; background-color: #EAEAEA;}
  #section2 {padding-top:30px;height:750px;color: #333333; background-color: #EAEAEA;}
</style>
<head>
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
	<link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>
<script type="text/javascript">
/*
1. Creat Objects of stock
2. set all stock objects into Array
3. claim global varaibles
*/
function stock(elementId,idx,share,mktValue){
	this.elementId = elementId;
	this.idx = idx;
	this.share = share;
	this.mktValue = mktValue;
}
var stk1 = new stock(1,"",0,0);
var stk2 = new stock(2,"",0,0);
var stk3 = new stock(3,"",0,0);
var stk4 = new stock(4,"",0,0);
var stk5 = new stock(5,"",0,0);
var stk6 = new stock(6,"",0,0);
var stk7 = new stock(7,"",0,0);
var stk8 = new stock(8,"",0,0);
var stk9 = new stock(9,"",0,0);
var stk10 = new stock(10,"",0,0);
var stk11 = new stock(11,"",0,0);

var stkArray = [stk1,stk2,stk3,stk4,stk5,stk6,stk7,stk8,stk9,stk10,stk11];
	
var initialDeposit = 0;
var domesticStocksValue = 0;
var overseaStockValue = 0;

/*
Function checkForm
Check uncompleted items
Check fulfilled items < 7
*/
function checkForm(){
	var fullFilled = true;
	var totalValue = 0;
	var value = 0;
	var trans_value = 0;

	var trans_symbol = "trans_symbol_";
	var trans_share = "trans_share_";	
		
	for(x=1;x<8;x++){
		trans_symbol_value = document.getElementById(trans_symbol + x).value;
		trans_share_value = document.getElementById(trans_share + x).value;
		if(trans_symbol_value == "" || trans_symbol_value == null || trans_share_value == "" || trans_share_value== null){
			fullFilled = false;
		}
	}
	for(x=8;x<11;x++){
		trans_symbol_value = document.getElementById(trans_symbol + x).value;
		trans_share_value = document.getElementById(trans_share + x).value;
		if((trans_symbol_value == "" && trans_share_value != "") || (trans_symbol_value != "" && trans_share_value == "")){
				fullFilled = false;
		}
	}
	if(fullFilled == false){
		alert("You have uncompleted item!\n 1. Check the Symbol and Share you've inputted \n 2.Share can not be number <= 0");
		return false;
	}
}
/*
Function creatPflo
Check uncompleted items
*/	
function creatPflo(){
	var newPfloName = document.getElementById('pflo_name').value;
	var newDeposit = document.getElementById('newDeposit').value;

	if(newPfloName == "" || newPfloName == null || newDeposit == "" || newDeposit == null || newDeposit <= 0 || newPfloName.length > 20){
		alert("Input Validation Error:\n 1. Please fill all blanks\n 2. Deposit can not be number <= 0\n 3. The portfolio name can not exceed 20 characters");
		return false;
	}
}

/*
Function checkDuplicateItem
Check duplicate item(same symbol)
*/	
function checkDuplicateItem(currentItemNo){
	var currentInputSymbol = document.getElementById("trans_symbol_" + currentItemNo).value;
	var i;
	var symbol;
		
	for(i=1;i<11;i++){
		if(i == currentItemNo) continue;
		symbol = document.getElementById("trans_symbol_" + i).value;
		if(symbol != "" && symbol != null && currentInputSymbol == symbol){
			alert("You have duplicate stocks in the list!");
			document.getElementById("trans_symbol_" + currentItemNo).value = "";
			break;
		}
	}
}

/*
Function calculateValue
value = price * rate * share
calculateValue()->checkBalance()->updateProgress()
*/	
function calculateValue(currentItemNo){
	var priceElement = "trans_price_" + currentItemNo;
	var rateElement = "trans_rate_" + currentItemNo;
	var shareElement = "trans_share_" + currentItemNo;
	var valueElement = "trans_value_" + currentItemNo;
	var idxElement = "trans_index_" + currentItemNo;

	var price = document.getElementById(priceElement).value;
	var rate = document.getElementById(rateElement).value;
	var share = document.getElementById(shareElement).value;
	var idx = document.getElementById(idxElement).value;

	if(share <= 0){
		alert("Share can not be 0 or nagative number");
		document.getElementById(shareElement).value = "";
	}
		
	if(price =="" || price == null || rate=="" || rate == null ||share == "" || share == null || share < 1 || idx == "" || idx == null) return false;
		
	var mktValue = parseFloat(price * rate * share).toFixed(2);
	stkArray[currentItemNo-1].idx = idx;
	stkArray[currentItemNo-1].share = share;
	stkArray[currentItemNo-1].mktValue = Number(mktValue);
	document.getElementById(valueElement).value = mktValue;
	//go to check the cash balance and select stock value
	checkBalance(currentItemNo);
}

/*
Function clearInputData
Clear specified inputs' value
*/
function clearInputData(currentItemNo){
	document.getElementById("trans_share_"+currentItemNo).value = "";
	document.getElementById("trans_value_"+currentItemNo).value = "";
	stkArray[currentItemNo-1].mktValue = 0;
	stkArray[currentItemNo-1].share = 0;
}

/*
Function checkBalance
Check the cash balance and the selected stock value
calculateValue()->checkBalance()->updateProgress()
checkBalance()->clearInputData()
*/
function checkBalance(currentItemNo){
	domesticStocksValue = 0;
	overseaStockValue = 0;

	for(j=0;j<stkArray.length;j++){
		if(stkArray[j].idx == "Dow 30" || stkArray[j].idx == "NYSE"){
			domesticStocksValue += Number(stkArray[j].mktValue);
		} else {
			overseaStockValue += Number(stkArray[j].mktValue);
		}
	}

	if(domesticStocksValue + overseaStockValue > initialDeposit){
		alert("The balance is not enough, please review & change your configuration!");
		//Clear current inputs
		clearInputData(currentItemNo);
	} else {
		//Update the data of the progress bar
		updateProgress();
	}
	return false;
}

/*
Function updateProgress
Update the status of the progress bar
*/
function updateProgress(){
	var portion;
	var balance;
	var balRemainPortion;
		
	portion = (domesticStocksValue /(domesticStocksValue + overseaStockValue)*100).toFixed(0);
	balance = (initialDeposit - overseaStockValue - domesticStocksValue).toFixed(0);
	balRemainPortion = ((balance / initialDeposit)*100).toFixed(0);
	
	//Update progress bar
	document.getElementById("stockPortionProgressBar").innerHTML = "Domestic stock value portion: " + portion + "%";
	document.getElementById("stockPortionProgressBar").style = "width:"+portion+"%";
	document.getElementById("pfloBalanceProgressBar").innerHTML = "Portfolio balance: " + balance;
	document.getElementById("pfloBalanceProgressBar").style = "width:"+balRemainPortion+"%";
	
	//clear data
	domesticStocksValue = 0;
	overseaStockValue = 0;
}

/*
Function loadStockList
Load the stock list for "Symbol" input element
*/
function loadStockList(itemNumber){
	//alert(itemNumber);
    var datalist = document.getElementById('json-datalist_'+itemNumber);
    var input = document.getElementById('trans_symbol_'+itemNumber);
    var request = new XMLHttpRequest();
	      
    request.onreadystatechange = function(response) {
    if (request.readyState == 4) {
        if (request.status == 200) {
        	console.log(request.responseText);
            var options = JSON.parse(request.responseText);
            console.log(options);
            options.forEach(function(item) {
	            var option = document.createElement('option');
	            option.value = item;
	            datalist.appendChild(option);
	        });
	        input.placeholder = "e.g. MMM";
	        } else {
	        	input.placeholder = "Couldn't load options";
	        	}
	        }
	    };
	input.placeholder = "Loading options...";
	request.open('GET', 'stockSymbol.json', true);
	request.send();
	return false;
}

/*
Function loadStockList
Load the index,close_price,exchange rate for specified "Symbol"
*/
function loadStkInfo(currentElement){
	checkDuplicateItem(currentElement);
	var symbol = document.getElementById("trans_symbol_"+currentElement).value;
	var returnData;
	var idxName;
	var price;
	var rate;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	        returnData = (this.responseText).split("|");
	        idxName = returnData[0];
	        price = returnData[1];
	        rate = returnData[2];

	        document.getElementById("trans_index_"+currentElement).value = idxName;
	        document.getElementById("trans_price_"+currentElement).value = price;
	        document.getElementById("trans_rate_"+currentElement).value = rate;
	    }
	};
	xmlhttp.open("GET", "loadStockInfo.php?symbol=" + symbol, true);
	xmlhttp.send();
}
</script>

  </head>

  <body>
  <nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
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

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<div id="section1" class="container-fluid">
  <h2>Step 1 - Creat a new Portfolio & Deposit Initial Amount</h2>
  <p>Please input a portfolio name:</p>
  <p><form class="form-group" method="post" action="startup_wizard.php"><input class="form-control" type="text" placeholder="New Portfolio Name" id="pflo_name" name="pflo_name"></p>
  <p>Please input the initial deposit amount, we recommend you to deposit 10K USD or above:</p>
  <p><input class="form-control" type="number" placeholder="10,000" id="newDeposit" name="newDeposit"></p>
  <p><button class="btn btn-success" type="submit" id="btn_newPortfolioName" onClick="return creatPflo()">SAVE</button></form></p>
  <p><?php echo $error_msg_1 ?></p>
</div>

<div id="section2" class="hidden">
  <h2>Step 2 - Configure your boost 7-10 Stocks</h2>
  <p>We recommend you to add minimum 7, maximum 10 stocks in a single portfolio.</p>
  <p>Portion of Domestic : Overseas is : 70% : 30%</p>
  
 <!--Progress bar start-->
 <!--Progress bar for domestic/oversea portion-->
    <div class="progress">
    <div id="stockPortionProgressBar" class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:60%">
    </div>
  </div>
  
 <!--Progress bar for portfolio balance-->
    <div class="progress">
    <div id="pfloBalanceProgressBar" class="progress-bar progress-bar" role="progressbar" aria-valuenow="12" aria-valuemin="0" aria-valuemax="100" style="width:50%">
    </div>
  </div>
 <!--Progress bar end-->
  
  <div class="table-responsive">
	<table class="table-condensed">
		<thead>
			<tr>
				<th>#</th>
				<th>Operation</th>
				<th>Symbol</th>
				<th>Share</th>
				<th>Index</th>
				<th>Date</th>
				<th>Price</th>
				<th>Rate</th>
				<th>Value</th>
			</tr>
			</thead>
            <tbody>
<!--Stock input line 1 -->
<form class="form-group" id="newTransForm" name="newTransForm" method="post" action="startup_save.php" target="iFrame_hidden">
<?php 
for($x=1;$x<11;$x++){
?>
<tr id="addTransItem_<?php echo $x?>">
	<td><?php echo $x?></td>
	<td><select class="form-control" id="trans_operation_<?php echo $x?>" disabled = "yes" readonly>
			<option value="1" selected="selected">Buy</option>
			<option value="0">Sell</option>
		</select></td>
	<td><input class="form-control" type="text" id="trans_symbol_<?php echo $x?>" name="trans_symbol_<?php echo $x?>" list="json-datalist_<?php echo $x?>" onClick="return loadStockList(<?php echo $x?>)" onBlur="return loadStkInfo(<?php echo $x?>)"></input></td>
		<datalist id="json-datalist_<?php echo $x?>" ></datalist></input></td>
	<td><input class="form-control" type="number" id="trans_share_<?php echo $x?>" name="trans_share_<?php echo $x?>" onChange="return calculateValue(<?php echo $x?>)"></input></td>
	<td><input class="form-control" type="text" id="trans_index_<?php echo $x?>" name="trans_index_<?php echo $x?>" readonly></input></td>
	<td><input class="form-control" type="date" id="trans_date_<?php echo $x?>" name="trans_date_<?php echo $x?>" value="2016-09-12" readonly></input></td>
	<td><input class="form-control" type="number" id="trans_price_<?php echo $x?>" name="trans_price_<?php echo $x?>" readonly></input></td>
	<td><input class="form-control" type="number" id="trans_rate_<?php echo $x?>" name="trans_rate_<?php echo $x?>" readonly></input></td>
	
	<td><input class="form-control" type="number" id="trans_value_<?php echo $x?>" name="trans_value_<?php echo $x?>" readonly  value="0"></input></td>
</tr>
<?php
}
?>
<tr><td colspan="4"><input type="hidden" id="pflo_name" name="pflo_name" value="<?php echo $pflo_name?>"><input type="hidden" id="newDeposit" name="newDeposit" value="<?php echo $newDeposit?>"></input><button type="submit" class="btn btn-success" onClick="return checkForm()">Save All</button></td></tr>
				</form>
              </tbody>
            </table>
          </div>
</div>
</div>
<?php 
	if($error_msg_1 == "success"){
		echo "<script language=\"javascript\">";
		echo "initialDeposit = $newDeposit;";
		echo "document.getElementById(\"section1\").className = \"hidden\";";
		echo "document.getElementById(\"section2\").className = \"container-fluid\";";
		echo"</script>";
	}
?>
</body></html>
<?php
$error_msg_1 = "";
$error_msg_2 = "";
require "conn_close.php";
?>