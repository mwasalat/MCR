<?php 
session_start();
include('dbconnection.php');
include('dbconnection_fleet.php');
if(isset($_POST["query"]))  
 {  
   $_SESSION['timeout'] = time();
    if(strlen($_POST["query"]) > 3)
    {
     
	  //$query = "SELECT * FROM cust_details WHERE fname like  '".$_POST["query"]."%' and fname!=''";  

    //  $query = "SELECT * FROM pri_cust_lang WHERE fname like  '".$_POST["query"]."%' and fname!='' and language='en'";  
   $query = "SELECT pri_cust.pri_cust_id,fname,glcod FROM pri_cust JOIN pri_cust_lang ON pri_cust.pri_cust_id=pri_cust_lang.pri_cust_id WHERE fname like  '%".$_POST["query"]."%' and fname!='' and language='en'"; 
      $result = mysqli_query($conn,$query);  
      $search_arr = array(); 
		  
      if(mysqli_num_rows($result) > 0)  
      {  
           while($fetch = mysqli_fetch_array($result))  
           {  
              $id =$fetch['pri_cust_id'];
              $fname = $fetch['fname'];
    $glcod = $fetch['glcod'];
              $search_arr[] = array("id" => $id, "fname" => $fname,"glcod"=>$glcod);
           }
             
           echo json_encode($search_arr);  
      }  
      
    }
    else
    {
      return null;
    }

     
 }  


?>