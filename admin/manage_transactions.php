<?php
session_start();
include '../dbconnection.php';
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

    <title>Admin | Manage Users</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
    
<link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
<style>
#example_filter{
    padding-right: 64px;

}

</style>

  </head>

  <body>

  <section id="container" >
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <a href="#" class="logo"><b>Admin Dashboard</b></a>
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
              	  	
                  <li class="mt">
                      <a href="change-password.php">
                          <i class="fa fa-file"></i>
                          <span>Change Password</span>
                      </a>
                  </li>

                

                  <li class="sub-menu">
                      <a href="manage_transactions.php" >
                          <i class="fa fa-users"></i>
                          <span>Manage Transactions</span>
                      </a>
                   
                  </li>


                
              <?php
              if ((strtolower($_SESSION['login'])=="naira" || strtolower($_SESSION['login'])=="pinto")) {
               
 

  ?>
                             <li class="sub-menu">
                      <a href="voidmcr.php" >
                          <i class="fa fa-stop"></i>
                          <span>Void MCR</span>
                      </a>
                   
                  </li>
                  <?php
                }
                  ?>
              
                 
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
	                  	  	  <h4><i class="fa fa-angle-right"></i> All User Details </h4>
	                  	  	  <hr>
                              <thead>
                              <tr>
                              <th>ID.</th>
                                  <th>SLno.</th>
                                 
                                  <th>Customer Name</th>
                                                                 
                                  <th>Email ID</th>
                                 <!--  <th>Cheque Number</th>
                                  <th>Bank Name</th> -->
                                <!--   <th>Invoice Number</th> -->
                                 
                                
                                   <th>Amount(Dhs)</th>

                                    <th>Date/Time</th>
                                     <th>Type</th>
                                     <th>Messenger</th>
                                <th>Status</th>
                                  <th>Download Invoice</th>

                              </tr>
                              </thead>
                              <tbody>
                    <?php $ret=mysqli_query($con,"select * from transaction order by idtransaction DESC LIMIT 200");
							
							  while($row=mysqli_fetch_array($ret))
							  {?>
                              <tr>
                              <td><?php echo $row['idtransaction'];?></td>
                                  <td><?php echo $row['trans_sl_no'];?></td>
                                  <td><?php echo $row['customername'];?></td>
                                  <td><?php echo $row['emailid'];?></td>  
                                  <!-- <td><?php echo $row['chqnumber'];?></td>
                                   <td><?php echo $row['bankname'];?></td> -->
                                   <!--  <td><?php echo $row['invoicenumber'];?></td> -->
                                    
                                     <td><b><?php echo $row['amount'];?></b></td>
                                       <td><?php echo $row['date_of_reg'];?></td>
                                       <td><?php echo $row['trans_type'];?></td>

                                     <td><?php echo $row['messenger'];?></td> 
  <td style="color:red"><?php echo $row['mcrstatus'];?></td> 
                                  <td align="center">
                                     
                                     <a href="../pdfs/MCR<?php echo $row['trans_sl_no'];?>.pdf" target="_blank"> 
                                     <button class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-download-alt"></i></button></a>
                                     
                                  </td>
                              </tr>
                              <?php  }?>
                             
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
   $('#example').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
    
} );


  </script>
  </body>
</html>
<?php } ?>