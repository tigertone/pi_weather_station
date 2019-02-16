<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$dataRange=$_GET["dataRange"];
$resamplingInterval=$_GET["resamplingInterval"];
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
// Get most recent data
if ($dataRange === "Today") {
$sql="SELECT GMT, decidegreesInternal, pressureInternal, humidityInternal, decidegreesExternal, humidityExternal FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 DAY) AND ID mod ".$resamplingInterval." = 0";
}

elseif ($dataRange === "Annual") {
$sql = "select * FROM dailyExtremes WHERE sampledDate > DATE_SUB(NOW(), INTERVAL 1 YEAR)";
}
$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));



if (mysqli_num_rows($result)!=0)
{

    $myArray =mysqli_fetch_all($result,MYSQLI_ASSOC);

} else {

    $myArray = "noData";

}

echo json_encode($myArray);

mysqli_close($conn);

?>
