<?php session_start();
require_once('dbconnection.php');
$loc="";
if(isset($_GET['server'] ))
{
	$_SESSION['server'] = $_GET['server'];
}

//Code for Registration 
if(isset($_POST['signup']))
{
	$fname=$_POST['fname'];
	$lname=$_POST['lname'];
	$email=$_POST['email'];
	$password=$_POST['password'];
	$contact=$_POST['contact'];
	$enc_password=$password;
	$msg=mysqli_query($con,"insert into users(fname,lname,email,password,contactno) values('$fname','$lname','$email','$enc_password','$contact')");
if($msg)
{
	echo "<script>alert('Register successfully');</script>";
}
}

// Code for login 
if(isset($_POST['login']))
{
$password=$_POST['password'];
$dec_password=$password;
$useremail=$_POST['uemail'];
//$location=$_POST['location'];
$ret= mysqli_query($con,"SELECT * FROM users WHERE email='$useremail' and password='$password'");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$extra="welcome.php";
$_SESSION['login']		=	$_POST['uemail'];
$_SESSION['location']	=	$num['location'];
$_SESSION['id']			=	$num['id'];
$loc			=	$num['location'];
$_SESSION['accesstype']	=	$num['access_type'];
$_SESSION['companycode']	=	$num['company_code'];
$accesstype=$num['access_type'];

$_SESSION['timeout'] = time();

$_SESSION['name']=$num['fname'];
$host=$_SERVER['HTTP_HOST'];
$uri=rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
if(strtolower($accesstype)=="carsale")
		{
			$extra="carsale.php";
		}
header("location:http://$host$uri/$extra");
exit();
}
else
{
$extra="index.php";
$host  = $_SERVER['HTTP_HOST'];
$uri  = rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
echo "<script>alert('Invalid username or password');</script>";
header("location:http://$host$uri/$extra");

exit();
}
}

//Code for Forgot Password

if(isset($_POST['send']))
{
$femail=$_POST['femail'];

$row1=mysqli_query($con,"select email,password from users where email='$femail'");
$row2=mysqli_fetch_array($row1);
if($row2>0)
{
$email = $row2['email'];
$subject = "Information about your password";
$password=$row2['password'];
$message = "Your password is ".$password;
mail($email, $subject, $message, "From: $email");
echo  "<script>alert('Your Password has been sent Successfully');</script>";
}
else
{
echo "<script>alert('Email not register with us');</script>";	
}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Login System</title>
<link href="css/style.css" rel='stylesheet' type='text/css' />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Elegent Tab Forms,Login Forms,Sign up Forms,Registration Forms,News latter Forms,Elements"./>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
</script>
<style>
select {
        width: 190px;
        height:50px;
        margin: 30px;
    }
     
</style>
<script src="js/jquery.min.js"></script>
<script src="js/easyResponsiveTabs.js" type="text/javascript"></script>
				<script type="text/javascript">
					$(document).ready(function () {
						$('#horizontalTab').easyResponsiveTabs({
							type: 'default',       
							width: 'auto', 
							fit: true 
						});
					});
				   </script>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,600,700,200italic,300italic,400italic,600italic|Lora:400,700,400italic,700italic|Raleway:400,500,300,600,700,200,100' rel='stylesheet' type='text/css'>
</head>
<body>
<div style="margin-left: 25%; margin-top:5px; font-size:14px; padding:10px; width:50%; color:red; background-color: whitesmoke;" id="blink"><center>Dear Team,<br/> Please note that we are moving this application to our new common portal <span style="font-weight:bold; color:#900"><a href="http://mcr.enguae.com/hub/" target="_blank">HUB</a></span> very soon.So the users using this application kindly share your employee no. to <a href="mailto:sanil.sadasivan@onetechnology.biz">sanil.sadasivan@onetechnology.biz</a> for the access of new application. </div>
<div class="main">
		<h1 style="font-size: 2em;">MCR System</h1>
	 <div class="sap_tabs">	
			<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
			  <ul class="resp-tabs-list">
			
				  <li class="resp-tab-item" aria-controls="tab_item-1" role="tab"><div class="top-img"><img src="images/top-lock.png" alt=""/></div><span>Login</span></li>
				
				  <div class="clear"></div>
			  </ul>		
			  	 
			
			 <div class="tab-2 resp-tab-content" aria-labelledby="tab_item-1">
					 	<div class="facts">
							 <div class="login">
							<div class="buttons">
								
								
							</div>
							<form name="login" action="" method="post">
								<input type="text" class="text" name="uemail" value="" placeholder="Enter your username/registered email" tabindex="1" ><a href="#" class=" icon email"></a>

								<input type="password" value="" name="password" placeholder="Enter valid password" tabindex="2"><a href="#" class=" icon lock"></a>

							<?php /* <select class="selectpicker" data-style="btn-primary" name="location"> //
							//  <option title="AUH" <?php if($loc == 'AUH') {?> selected='selected' <?php } ?> >AUH</option> //
							//  <option title="DXB" <?php if($loc == 'DXB') {?> selected='selected' <?php } ?> >DXB</option> //
							// </select> */ ?>


								<div class="p-container">
								
									<div class="submit two">
									<input type="submit" name="login" value="LOG IN" tabindex="3">
									</div>
									<div class="clear"> </div>
								</div>

							</form>
					</div>
				</div> 
			</div> 			        					 
				 <!-- <div class="tab-2 resp-tab-content" aria-labelledby="tab_item-1">
					 	<div class="facts">
							 <div class="login">
							<div class="buttons">
								
								
							</div>
							<form name="login" action="" method="post">
								<input type="text" class="text" name="femail" value="" placeholder="Enter your username/registered email" required  ><a href="#" class=" icon email"></a>
									
										<div class="submit three">
											<input type="submit" name="send" onClick="myFunction()" value="Send Email" >
										</div>
									</form>
									</div>
				         	</div>           	      
				        </div>	 -->
				     </div>	
		        </div>
	        </div>
	     </div>

</body>
</html>