<?php
session_start();
include'dbconnection.php';
// checking session is valid for not 
if (strlen($_SESSION['id']==0)) {
  header('location:logout.php');
  } else{

// for deleting user
if(isset($_GET['id']))
{
$adminid=$_GET['id'];
$msg=mysqli_query($con,"delete from users where id='$adminid'");
if($msg)
{
echo "<script>alert('Data deleted');</script>";
}
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Messenger | Manage Transactions</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
    
<link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">

  </head>

  <body>

  <section id="container" >
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <a href="#" class="logo"><b>Messenger Dashboard</b></a>
            <div class="nav notify-row" id="top_menu">
               
                         
                   
                </ul>
            </div>
            <div class="top-menu">
            	<ul class="nav pull-right top-menu">
                    <li><a class="logout" href="logout.php">Logout</a></li>
            	</ul>
            </div>
        </header>
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <ul class="sidebar-menu" id="nav-accordion">
              
              	  <p class="centered"><a href="#"><img src="assets/img/ui-sam.jpg" class="img-circle" width="60"></a></p>
              	  <h5 class="centered"><?php echo $_SESSION['login'];?></h5>
              	  	
                  <!-- <li class="mt">
                      <a href="change-password.php">
                          <i class="fa fa-file"></i>
                          <span>Change Password</span>
                      </a>
                  </li>

                  <li class="sub-menu">
                      <a href="manage-users.php" >
                          <i class="fa fa-users"></i>
                          <span>Manage Users</span>
                      </a>
                   
                  </li> -->

                  <li class="sub-menu">
                      <a href="manage_transactions.php" >
                          <i class="fa fa-users"></i>
                          <span>Manage Transactions</span>
                      </a>
                   
                  </li>
              
                 
              </ul>
          </div>
      </aside>
      <section id="main-content">
          <section class="wrapper">
          	<h3><i class="fa fa-angle-right"></i> Manage Transactions</h3>
				<div class="row">
				
                  
	                  
                  <div class="col-md-12">
                      <div class="content-panel">
                          <table id="example" class="display" style="width:100%" >
	                  	  	  <h4><i class="fa fa-angle-right"></i> All Transaction Details </h4>
	                  	  	  <hr>
                              <thead>
                              <tr>
                                  <th>Sno.</th>
                                  <th class="hidden-phone">Transaction ID</th>
                                  <th>Customer Name</th>
                                                                 
                                  <th>Email ID</th>
                                  <!-- <th>Cheque Number</th>
                                  <th>Bank Name</th> -->
                                  <th>Invoice Number</th>
                                  <th>Amount</th>
                                   <th>Date/Time</th>

                                  <th>Download</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php 
                              $mname = $_SESSION['login'];
                              $ret=mysqli_query($con,"select * from transaction where messenger = '$mname' order by idtransaction desc");
							  $cnt=1;
							  while($row=mysqli_fetch_array($ret))
							  {?>
                              <tr>
                              <td><?php echo $cnt;?></td>
                                  <td><?php echo $row['idtransaction'];?></td>
                                  <td><?php echo $row['customer_phone'];?></td>
                                  <td><?php echo $row['emailid'];?></td>  
                                 <!--  <td><?php echo $row['chqnumber'];?></td>
                                   <td><?php echo $row['bankname'];?></td> -->
                                    <td><?php echo $row['invoicenumber'];?></td>
                                     <td><?php echo $row['amount'];?></td>
                                      <td><?php echo $row['date_of_reg'];?></td>
                                      
                                  <td>
                                     
                                     <a href="../pdfs/<?php echo $row['invoicenumber'];?>.pdf" target="_blank"> 
                                     <button class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-download-alt"></i></button></a>
                                     <!-- <a href="manage-users.php?id=<?php echo $row['idtransaction'];?>"> 
                                     <button class="btn btn-danger btn-xs" onClick="return confirm('Do you really want to delete');"><i class="fa fa-trash-o "></i></button></a> -->
                                  </td>
                              </tr>
                              <?php $cnt=$cnt+1; }?>
                             
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
		</section>
      </section
  ></section>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="assets/js/common-scripts.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script>
      $(function(){
          $('select.styled').customSelect();
      });


$(document).ready(function() {
    $('#example').DataTable();
} );



  </script>

  </body>
</html>
<?php } ?>