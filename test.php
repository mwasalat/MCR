<?php
session_start();
require_once('dbconnection.php');
$query = "SELECT locode FROM pri_cust WHERE pscode=6178";
        $result = mysqli_query($con1,$query);  
        $fetch = mysqli_fetch_array($result);
        
       echo $_SESSION['auh_pdc'] =$fetch['locode'];
	   
       /* $_SESSION['auh_cdc'] =$fetch['auh_cdc'];
        $_SESSION['dxb_pdc'] =$fetch['dxb_pdc'];
        $_SESSION['dxb_cdc'] =$fetch['dxb_cdc']; *?
?>