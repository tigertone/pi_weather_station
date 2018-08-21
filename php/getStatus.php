<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername="localhost";
$username="database_reader";
$password="PASSWORD";
$dbname="weather_records";

// Create connection
$conn=mysqli_connect($servername, $username, $password, $dbname);



// Check connection
if (!$conn) {
die( "Connection failed: " . mysqli_connect_error());
}

$result=mysqli_query($conn, "SELECT ID FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 70 SECOND)") or die(mysqli_error($conn));

if (mysqli_num_rows($result)!=0)
{
    echo 1;
} else 
{
    echo 0;
}


mysqli_close($conn);

?>
