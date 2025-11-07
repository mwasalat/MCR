<?php

session_start();
include('dbconnection.php');

require_once('class.phpmailer.php');
define ("NODE_TYPE_START",0);
define ("NODE_TYPE_ELEMENT",1);
define ("NODE_TYPE_ENDELEMENT",2);
define ("NODE_TYPE_TEXT",3);
define ("NODE_TYPE_COMMENT",4);
define ("NODE_TYPE_DONE",5);


	$mcrid=$_POST['id'];
	$status="voided";
	$location="";
	$txtcustomer="";


 if(isset($_SESSION['login']))
 {
 $sql_cc="UPDATE transaction SET mcrstatus='$status',changedby='".$_SESSION['login']."' WHERE trans_sl_no='".$mcrid."' ";
 }
          
 $result_cc = mysqli_query($con,$sql_cc);
if(mysqli_affected_rows($con) >0 )
{



      $query = "SELECT * FROM transaction  where trans_sl_no='$mcrid'";
      $result = mysqli_query($con,$query);  
      $search_arr = array(); 
	
      if(mysqli_num_rows($result) > 0)  
      {  
           while($fetch = mysqli_fetch_array($result))  
           {  
              $emailid =$fetch['emailid'];
              $location = $fetch['location'];
              $txtcustomer=$fetch['customername'];
              
           }
             
            
      }

   








    echo "1";
   
}
else
{
	echo "0";
}
?>