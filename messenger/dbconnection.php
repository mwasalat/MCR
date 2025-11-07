<?php
define('DB_SERVER','remotemysql.com');
define('DB_USER','3ovVCyOQKl');
define('DB_PASS' ,'YE9ydQRm8W');
define('DB_NAME', '3ovVCyOQKl');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
 }

?>

