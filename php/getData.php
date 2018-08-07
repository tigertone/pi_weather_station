<?php 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$q=$_GET["q"];

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

if ($q === "Today") {
$sql="SELECT GMT, decidegrees, pressure, humidity FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 DAY)" ;
}
elseif ($q === "Annual") {
$sql = "select * FROM dailyExtremes WHERE sampledDate > DATE_SUB(NOW(), INTERVAL 1 YEAR)";
}

$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));


$myArray=array();
    if ($result) {

    $myArray =mysqli_fetch_all($result,MYSQLI_ASSOC);

}


echo json_encode($myArray);

mysqli_close($conn);

?>
