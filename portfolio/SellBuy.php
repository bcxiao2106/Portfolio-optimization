<?php
  require("conn.php");
?>


  <?php

  //if(isset($_POST['sub'])){
    //get and save the parameters 
    $date = date("Y-m-d");
    /*
    $type = $_POST['transType'];
    $name = $_POST['symbol'];
    $share = $_POST['sShare'];
    $pid = $_POST['pid'];
    $uid = $_POST['uid'];*/
    $a = $_POST['dataArray'];
    $a = json_decode($a, true);
    $array = Array();
    
    $arrayNum = 0;
    foreach($a as $u) {
      //$u = json_decode($u,true);
      $array[$arrayNum] = $u;
      //echo $array[$arrayNum];
      $arrayNum++; 
    }
    
    $pid = $array[0];
    $uid = $array[1];
    $share = $array[2];
    $type = $array[3];
    $name = $array[4];

    if($share == "n" && $type == "Sell") {
      $share = 0;
      $arrayRow = ($arrayNum - 5)/3;
      for ($i = 0; $i < $arrayRow; $i++) {
        $share = $share + $array[$i * 3 + 7];
      }
      //echo $share;
    } 

    
    if($type != "" && $name != "" && $date != "" && $share != 0 && $pid != "" && $uid != "") {
    
      $sql = "SELECT stk_info.current_price, stk_info.stk_id, stk_info.idx_id FROM stk_info WHERE stk_info.symbol = \"$name\"";    
      $result = $conn->query($sql);
      
      //check if the stock symbol is valid
      if($result->num_rows > 0) {
      
      while($row = $result->fetch_assoc()){
        $price = $row[current_price];
        $sid = $row[stk_id];
        $currencyId = $row[idx_id];
      }
      
      //find the rate
      $sql10 = "SELECT currency.rate FROM currency WHERE currency.currency_id = $currencyId";   
      $result10 = $conn->query($sql10);
      if($result10->num_rows > 0) {
        while($row10 = $result10->fetch_assoc()){    // 1 SGD = rate USD; 1 INR = rate USD;
          $rate = $row10[rate];
        }
      }     
      
        //validate cash vs buy
        if($type == "Buy") {
        
          //get the portfolio's cash balance
          $sql1 = "SELECT portfolio.cash_balance FROM portfolio WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
          $result1 = $conn->query($sql1);
          if($result1->num_rows > 0) {
            while($row1 = $result1->fetch_assoc()){
              $cashBal = $row1[cash_balance];
            }
            
            //calculate price with currency
            $priceCurr = $price * $rate;
            $priceCurrTotal = $price * $rate * $share;
            
            
            if($priceCurrTotal > $cashBal) {
            //send alert
              //echo "<script language=\"javascript\">alert('Your transaction has exceeded! Please check your account!'); location.href=\"main.php?pid=$pid\";</script>";
              echo "Your transaction has exceeded! Please check your account!";
             } else {
              //insert data to pflo_transaction
              $sql5 = "INSERT INTO pflo_transaction (pflo_id, stk_id, oprt_type, share, price, cash_value, date) VALUES ('$pid', '$sid', '$type', '$share', '$priceCurr', '$priceCurrTotal', \"$date\")"; 
              $result5 = $conn->query($sql5);
            
              //update the cash balance in portfolio
              $balance = $cashBal - $priceCurrTotal;
              $sql2 = "UPDATE portfolio SET cash_balance = $balance WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
              $result2 = $conn->query($sql2);
            
              //update pflo_stk_info, case 1: this stock is already in the portfolio
              $sql3 = "SELECT pflo_stk_info.share FROM pflo_stk_info WHERE pflo_stk_info.pflo_id = $pid AND pflo_stk_info.stk_id = $sid";
              $result3 = $conn->query($sql3);
              if($result3->num_rows > 0) {
                while($row3 = $result3->fetch_assoc()){
                  $share1 = $row3[share];
                  $shareNew = $share1 + $share;
                  $sql4 = "UPDATE pflo_stk_info SET pflo_stk_info.share = $shareNew WHERE pflo_stk_info.pflo_id = $pid AND pflo_stk_info.stk_id = $sid";
                  $result4 = $conn->query($sql4);
                }
                $sql14 = "SELECT pflo_trans_result.pur_price, pflo_trans_result.share FROM pflo_trans_result WHERE pflo_trans_result.pflo_id = $pid AND pflo_trans_result.stk_id = $sid AND pflo_trans_result.date = \"$date\"";
                $result14 = $conn->query($sql14);
                if($result14->num_rows > 0) {
                  while($row14 = $result14->fetch_assoc()){
                    //case 1: purchase on the same day with same price, then update the share
                    if($row14[pur_price] == $priceCurr) {
                    //find the share in plo_trans_result
                      $oldShare = $row14[share];
                      $sql15 = "UPDATE pflo_trans_result SET pflo_trans_result.share = ($oldShare + $share) WHERE pflo_trans_result.pflo_id = $pid AND pflo_trans_result.stk_id = $sid AND pflo_trans_result.date = \"$date\"";
                      $result15 = $conn->query($sql15);
                    }//case 2: purchase on the same day but different price, add a new row
                    else{
                      //update pflo_trans_result
                      $sql16 = "INSERT INTO pflo_trans_result (pflo_id, stk_id, date, share, pur_price) VALUES ('$pid', '$sid', \"$date\", '$share','$priceCurr')";
                      $result16 = $conn->query($sql16);
                    }
                  }
                } else{  //case 3:purchase on a different day
                  //update pflo_trans_result
                  $sql17 = "INSERT INTO pflo_trans_result (pflo_id, stk_id, date, share, pur_price) VALUES ('$pid', '$sid', \"$date\", '$share','$priceCurr')";
                  $result17 = $conn->query($sql17);
                }
              }else { // case 2: this stock is not in the porfolio before
                //update pflo_stk_info
                $sql4 = "INSERT INTO pflo_stk_info (pflo_id, stk_id, share, currency_id) VALUES ('$pid', '$sid', '$share', '$currencyId')";
                $result4 = $conn->query($sql4);
                //update pflo_trans_result
                $sql13 = "INSERT INTO pflo_trans_result (pflo_id, stk_id, date, share, pur_price) VALUES ('$pid', '$sid', \"$date\", '$share','$priceCurr')";
                $result13 = $conn->query($sql13);
              }
              //echo "<script language=\"javascript\"> location.href=\"main.php?pid=$pid\"; </script>";
              echo "Success!";
            }           
          } else {
            //echo "<script language=\"javascript\">alert('Cash in the portfolio cannot be retreived by now.'); location.href=\"main.php?pid=$pid\"; </script>";
            echo "Cash in the portfolio cannot be retreived by now.";
          }
        }
        
        else if ($type == "Sell") {
          //get the portfolio's cash balance
          $sql12 = "SELECT portfolio.cash_balance FROM portfolio WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
          $result12 = $conn->query($sql12);
          if($result12->num_rows > 0) {
            while($row12 = $result12->fetch_assoc()){
              $cashBal = $row12[cash_balance];
            }
          }
            $sql9 = "SELECT pflo_stk_info.share FROM pflo_stk_info WHERE pflo_stk_info.pflo_id = $pid AND pflo_stk_info.stk_id = $sid";
            $result9 = $conn->query($sql9);
            if($result9->num_rows > 0) {
              while($row3 = $result9->fetch_assoc()){
                $share1 = $row3[share] - $share;
                //find the rate
                $sql10 = "SELECT currency.rate FROM currency WHERE currency.currency_id = $currencyId";   
                $result10 = $conn->query($sql10);
                if($result10->num_rows > 0) {
                  while($row10 = $result10->fetch_assoc()){    // 1 SGD = rate USD; 1 INR = rate USD;
                    $rate = $row10[rate];
                  }
                 }
                 //calculate price with currency
                 $priceCurr = $price * $rate;
                 $priceCurrTotal = $price * $rate * $share;
                
                if($share1 > 0) { //update pflo_stk_info, case 1: this stock is already in the portfolio
                
                  $sql4 = "UPDATE pflo_stk_info SET pflo_stk_info.share = $share1 WHERE pflo_stk_info.pflo_id = $pid AND pflo_stk_info.stk_id = $sid";
                  $result4 = $conn->query($sql4);
                  
                  //insert data to pflo_transaction
                  $sql1 = "INSERT INTO pflo_transaction (pflo_id, stk_id, oprt_type, share, price, cash_value, date) VALUES ('$pid', '$sid', '$type', '$share', '$priceCurr', '$priceCurrTotal', \"$date\")"; 
                  $result1 = $conn->query($sql1);
          
                  //update cash balance in portfolio
                  $balance = $cashBal + $priceCurrTotal;
                  $sql2 = "UPDATE portfolio SET portfolio.cash_balance = $balance WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
                  $result2 = $conn->query($sql2);
                  
                  //update pflo_trans_result
                  $arrayRow = ($arrayNum - 5)/3;
                  for ($i = 0; $i < $arrayRow; $i++) {
                    $temp1 = $array[$i * 3 + 5]; //date
                    $temp2 = $array[$i * 3 + 6];  //price
                    $temp3 = $array[$i * 3 + 7];  //share
                    $sql19 = "SELECT pflo_trans_result.share FROM pflo_trans_result WHERE pflo_trans_result.pflo_id = $pid AND pflo_trans_result.stk_id = $sid AND pflo_trans_result.date = \"$temp1\" AND pflo_trans_result.pur_price = $temp2";
                    $result19 = $conn->query($sql19);
                    if($result19->num_rows > 0) {
                      while($row19 = $result19->fetch_assoc()){
                        if($row19[share] == $temp3) { //delete row
                          $sql20 = "DELETE FROM pflo_trans_result WHERE pflo_trans_result.pflo_id = $pid AND pflo_trans_result.stk_id = $sid AND pflo_trans_result.date = \"$temp1\" AND pflo_trans_result.pur_price = $temp2";
                          $result20 = $conn->query($sql20);
                        } else{  //update row
                          $sql18 = "UPDATE pflo_trans_result SET pflo_trans_result.share = ($row19[share] - $temp3) WHERE pflo_trans_result.pflo_id = $pid AND pflo_trans_result.stk_id = $sid AND pflo_trans_result.date = \"$temp1\" AND pflo_trans_result.pur_price = $temp2";
                          $result18 = $conn->query($sql18);
                        }
                      }
                    }
                    
                  }
                  
                  //echo "<script language=\"javascript\"> location.href=\"main.php?pid=$pid\"; </script>";
                  echo "Success";
                } else if ($share1 == 0){
                  //delete pflo_stk_info
                  $sql3 = "DELETE FROM pflo_stk_info WHERE pflo_stk_info.pflo_id = $pid AND pflo_stk_info.stk_id = $sid";
                  $result3 = $conn->query($sql3);
                  
                  //insert data to pflo_transaction
                  $sql1 = "INSERT INTO pflo_transaction (pflo_id, stk_id, oprt_type, share, price, cash_value, date) VALUES ('$pid', '$sid', '$type', '$share', '$priceCurr', '$priceCurrTotal', \"$date\")"; 
                  $result1 = $conn->query($sql1);
                  
                  //update cash balance in portfolio
                  $balance = $cashBal + $priceCurrTotal;
                  $sql2 = "UPDATE portfolio SET cash_balance = $balance WHERE portfolio.pflo_id = $pid AND portfolio.user_id = $uid";
                  $result2 = $conn->query($sql2);
                  
                  
                  //echo "<script language=\"javascript\"> location.href=\"main.php?pid=$pid\"; </script>";
                  echo "Success!";
                } else if($share1 < 0) {
                  //echo "<script language=\"javascript\">alert('Your transaction has exceeded! Please check your account!'); location.href=\"main.php?pid=$pid\";</script>";
                  echo "Your transaction has exceeded! Please check your account!";
                }
              }
            }else { // case 2: this stock is not in the porfolio before
              //echo "<script language=\"javascript\">alert('Your do not have this stock in your porfolio! Please check your account!'); location.href=\"main.php?pid=$pid\";</script>";
              echo "Your do not have this stock in your porfolio! Please check your account!";
            }      
          
        }
      
    } else {
      //echo "<script>alert('The stock is not valid! Please choose from the list!'); location.href=\"main.php?pid=$pid\";</script>";
      echo "The stock is not valid! Please choose from the list!";
    }
    }else {
      //echo "<script>alert('All the fields are required!'); location.href=\"main.php?pid=$pid\";</script>";
      echo "All the fields are required!";
    }
  
  
  require "conn_close.php";

  ?>
