<?php
session_start();
$inactive = 600; // check
if(isset($_SESSION['timeout']) )
 {
	$session_life = time() - $_SESSION['timeout'];

	if($session_life > $inactive) 
	{
		echo "0" ;
	}

	else 
	{
		echo "1" ;
	}
}
else
{
  $_SESSION['timeout'] = time();
  	echo "1" ;
 
}

?>