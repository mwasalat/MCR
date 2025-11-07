<?php 
session_start();
include('dbconnection.php');
include('dbconnection_fleet.php');
if(isset($_POST["userid"]))  
 {  
 	    $userid = $_POST['userid'];
     // $query = "SELECT * FROM cust_details WHERE pri_cust_id  =  '".$_POST["userid"]."'  and fname!=''";  
   $query = "SELECT pri_cust.pri_cust_id,pscode,phone1,tlxno,glcod,fname,addr1,addr2 FROM pri_cust JOIN pri_cust_lang ON pri_cust.pri_cust_id=pri_cust_lang.pri_cust_id WHERE pri_cust.pri_cust_id  =  '".$_POST["userid"]."'  and fname!=''";  

      $result = mysqli_query($conn,$query);  
      $return_arr  = array();
      if(mysqli_num_rows($result) > 0)  
      {  
           while($fetch = mysqli_fetch_array($result))  
           {  
              $pscode =$fetch['pscode'];
              $glcod =$fetch['glcod'];
              $id =$fetch['pri_cust_id'];
              $fname = $fetch['fname'];
              $email = $fetch['tlxno'];
              $phone1 = $fetch['phone1'];
              $search_arr[] = array("id" => $id,"pscode" => $pscode,"glcod" => $glcod, "fname" => $fname,"email" => $email,"phone1" => $phone1);
           }
           echo json_encode($search_arr);  
      }  
 }  
?>