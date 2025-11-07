<?php
define('DB_SERVER','130.61.83.59');
define('DB_USER','root');
define('DB_PASS' ,'F@st{rent}17');
define('DB_NAME', 'mcr');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);


// Check connection
if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
 }



?>
