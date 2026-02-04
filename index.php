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
<title>MCR System - Mwasalat</title>
<link href="css/style.css" rel='stylesheet' type='text/css' />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: url("images/bg.jpg") no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

.support-banner {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 15px 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 600px;
    width: 90%;
    border-left: 4px solid #e74c3c;
}

.support-banner p {
    color: #333;
    font-size: 14px;
    line-height: 1.6;
}

.support-banner a {
    color: #e74c3c;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.support-banner a:hover {
    color: #c0392b;
    text-decoration: underline;
}

.login-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    width: 100%;
    max-width: 420px;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    padding: 40px 30px;
    text-align: center;
}

.login-header img {
    max-width: 200px;
    height: auto;
    margin-bottom: 20px;
    filter: brightness(0) invert(1);
}

.login-header h2 {
    color: #fff;
    font-size: 24px;
    font-weight: 600;
    margin: 0;
    letter-spacing: 1px;
}

.login-header p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    margin-top: 8px;
}

.login-form {
    padding: 40px 35px;
}

.input-group {
    position: relative;
    margin-bottom: 25px;
}

.input-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 18px;
    transition: color 0.3s ease;
}

.input-group input {
    width: 100%;
    padding: 15px 15px 15px 50px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.input-group input:focus {
    outline: none;
    border-color: #e74c3c;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
}

.input-group input:focus + i,
.input-group input:focus ~ i {
    color: #e74c3c;
}

.input-group input::placeholder {
    color: #aaa;
}

.login-btn {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
}

.login-btn:active {
    transform: translateY(0);
}

.footer-text {
    text-align: center;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #eee;
    color: #888;
    font-size: 13px;
}

@media (max-width: 480px) {
    .login-container {
        margin: 10px;
    }
    
    .login-header {
        padding: 30px 20px;
    }
    
    .login-header img {
        max-width: 150px;
    }
    
    .login-form {
        padding: 30px 25px;
    }
    
    .support-banner {
        padding: 12px 20px;
        font-size: 13px;
    }
}
</style>
</head>
<body>

<!-- <div class="support-banner">
    <p><i class="fas fa-headset"></i> For any technical issue, please contact 
    <a href="mailto:osamah.kenawy@mwasalat.ae">osamah.kenawy@mwasalat.ae</a> or 
    <a href="mailto:alpana.soni@mwasalat.ae">alpana.soni@mwasalat.ae</a></p>
</div> -->

<div class="login-container">
    <div class="login-header">
        <img src="images/mwasalat_holding_logo.png" alt="Mwasalat Logo">
        <h2>MCR System</h2>
        <p>Sign in to your account</p>
    </div>
    
    <form class="login-form" name="login" action="" method="post">
        <div class="input-group">
            <input type="text" name="uemail" placeholder="Enter your username or email" tabindex="1" required>
            <i class="fas fa-envelope"></i>
        </div>
        
        <div class="input-group">
            <input type="password" name="password" placeholder="Enter your password" tabindex="2" required>
            <i class="fas fa-lock"></i>
        </div>
        
        <button type="submit" name="login" class="login-btn" tabindex="3">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
        
        <div class="footer-text">
            <p>Mwasalat &copy; <?php echo date('Y'); ?> - All Rights Reserved</p>
        </div>
    </form>
</div>

</body>
</html>