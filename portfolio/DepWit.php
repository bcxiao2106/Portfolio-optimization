
<?php
require "conn.php"; 

?>

<?php

if(isset($_POST['sub1'])){
  //get and save the parameters
  $number = $_POST['cashNumber'];
  //$date = $_POST['cashDate'];
  $date = date("Y-m-d");
  $type = $_POST['cashType'];
  $pid = $_POST['pid'];
  $uid = $_POST['uid'];
  
  echo $number;
  echo $date;
  echo $type;
  echo $pid;
  echo $uid;
  
  // $cashPo is the cash left in the porfolio
  //validate if all fields are fullfilled
  if($number != null & $date != null){
    
    
    //validate if the portfolio is selected
  
    //find the current cash balance
    $sql1 = "SELECT portfolio.cash_balance FROM portfolio WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
    $result1 = $conn->query($sql1);
    if($result1->num_rows > 0) {
      while($row1 = $result1->fetch_assoc()){
        $cashPo = $row1[cash_balance];
        echo $cashPo;
      }
      
      if($type == "Withdraw"){
      //validate if the user withdraw excess the cash in the portfolio
      if ($cashPo > $number) {
        $balance = $cashPo - $number;
        //update cash balance
        $sql4 = "UPDATE portfolio SET cash_balance = $balance WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
        $result4 = $conn->query($sql4);
        //insert data to DB
        $sql = "INSERT INTO pflo_transaction (pflo_id, stk_id, oprt_type, share, price, cash_value, date) VALUES ('$pid', NULL, '$type', NULL, NULL, '$number', '$date')";
        $result = $conn->query($sql);
        
        echo "<script language=\"javascript\"> location.href=\"main.php?pid=$pid\"; </script>";
        } else {  //not enough
            echo "<script language=\"javascript\"> alert(\"Withdraw failed! Money is not enough!\"); location.href=\"main.php?pid=$pid\";</script>";
        }
    }
    
    if($type == "Deposit"){
        $balance = $cashPo + $number;
        //update cash balance
        $sql4 = "UPDATE portfolio SET cash_balance = $balance WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
        $result4 = $conn->query($sql4);
        //insert data to DB
        $sql = "INSERT INTO pflo_transaction (pflo_id, stk_id, oprt_type, share, price, cash_value, date) VALUES ('$pid', NULL, '$type', NULL, NULL, '$number', '$date')";
        $result = $conn->query($sql);
        echo "<script language=\"javascript\"> location.href=\"main.php?pid=$pid\"; </script>";
      }
    }
    
        
    
  } else {
    echo "<script language=\"javascript\"> alert(\"All the fields are required!\"); \"main.php?pid=$pid\"; </script>";
  }
  
  
}

    require "conn_close.php";
?>
        
