<?php 
session_start();
include('dbconnection.php');
if(isset($_POST["query"]))  
 {  
 	  $output = '';  
      $query = "SELECT * FROM users WHERE contactno LIKE '".$_POST["query"]."%'";  
      $result = mysqli_query($con,$query);  
      $output = '<select id=clist>';  
      if(mysqli_num_rows($result) > 0)  
      {  
           while($row = mysqli_fetch_array($result))  
           {  
           		$output .= '<option value='.$row["email"].'>'.$row["email"].'</option>';
           }  
      }  
      else  
      {  
           $output .= '<li>Country Not Found</li>'; 
      }  
     $output .= '</select>'; 
      echo $output;
 }  


?>