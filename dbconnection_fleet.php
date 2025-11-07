

<?php
$servername   = "130.61.83.59";
$database = "fraclive";
$username = "root";
$password = "F@st{rent}17";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
// Check connection
if ($conn->connect_error) {
	echo "sds";
   die("Connection failed: " . $conn->connect_error);
}
  //echo "Connected successfully";
?>