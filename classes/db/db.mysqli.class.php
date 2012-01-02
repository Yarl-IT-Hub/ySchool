<?php 
include("data.php");
$mysqli = new mysqli($DatabaseServer, $DatabaseUsername, $DatabasePassword, $DatabaseName);
if (mysqli_connect_errno()) {
echo("Failed to connect, the error message is : ".
mysqli_connect_error());
exit();
}
?>