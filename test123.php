<?php
session_start();
echo ("Hello World!   ");
require_once('dbconnection.php');
echo phpinfo();
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo $row;
  }
} else {
  echo "0 results";
}




 /* Select loc from users table  `Loc` 
		$query = "SELECT  * FROM `users`";  WHERE `email`=`dxbtest`;"  
		$result = mysqli_query($con,$query); 
	
		$fetch = mysqli_fetch_array($result);
		echo "Test:"
		echo $fetch;
		 echo $_SESSION['email'] =$fetch['email']; 
echo "Returned rows are123: " . $result -> num_rows; */

/*
$query = "SELECT * FROM `parameters`";
// $query = "SELECT 'auh_pdc','auh_rest','dxb_pdc','dxb_rest' FROM `parameters`";
        $result = mysqli_query($con,$query);  
        $fetch = mysqli_fetch_array($result);
		
	echo $_SESSION['auh_rest'] =$fetch['auh_rest'];	
	echo $_SESSION['auh_pdc'] =$fetch['auh_pdc'];
    echo $_SESSION['dxb_pdc'] =$fetch['dxb_pdc'];
    echo $_SESSION['dxb_rest'] =$fetch['dxb_rest']; 
	echo ("Hello World");
echo "Returned rows are: " . $result -> num_rows;

*/


//$query1 = "SELECT * FROM `transaction`";	
//$result = mysqli_query($con,$sql);

	//trans_sl_no, location, chqdate
 // $sql = "INSERT INTO `transaction`( `trans_sl_no`,`customer_phone`, `customername`, `trans_type`, `emailid`, `chqnumber`,`chqdate`, `bankname`, `invoicenumber`, `amount`,`messenger`)   VALUES ('$slno','$txtCustomer','$customername','$trans_type','$emailid','$chqnumber','$chqdate','$bankname','$invoicenumber','$amount','$messenger')";
 
  $sql = "INSERT INTO `transaction`( `trans_sl_no`,`location`, `chqdate`,`customername`, `amount`,`messenger`)   VALUES ('1260','DUBXB','11-08-2020','EASTERN','11.11','ASHRAF')";
 $result = mysqli_query($con,$sql);  
     
		
		
       
?>