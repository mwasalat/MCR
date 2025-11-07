<?php 
session_start();
include('dbconnection.php');
include('dbconnection_fleet.php');
if(isset($_POST["query"]))  
 {  
   $_SESSION['timeout'] = time();
    if(strlen($_POST["query"]) > 2)
    {
     
	  //$query = "SELECT * FROM cust_details WHERE fname like  '".$_POST["query"]."%' and fname!=''";  

    //  $query = "SELECT * FROM pri_cust_lang WHERE fname like  '".$_POST["query"]."%' and fname!='' and language='en'";  
   $query = "SELECT chass,regno,manfu,model FROM veh_file LEFT JOIN veh_type ON veh_type.vtcode=veh_file.vtcode WHERE fltno='".$_POST["query"]."'"; 
      $result = mysqli_query($conn,$query);  
      $search_arr = array(); 
		  
      if(mysqli_num_rows($result) > 0)  
      {  
           while($fetch = mysqli_fetch_array($result))  
           {  
              $chass =$fetch['chass'];
              $regno = $fetch['regno'];
              $maufacturer=$fetch['manfu'];
              $model=$fetch['model'];
  
              $search_arr[] = array("chass" => $chass, "regno" => $regno, "maufacturer" => $maufacturer, "model" => $model);
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