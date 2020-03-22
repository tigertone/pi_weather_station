<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$startTime = microtime(true);
$dataRange=$_GET["dataRange"];
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
$queryFields = array("GMT", "decidegreesInternal", "pressureInternal", "humidityInternal", "decidegreesExternal", "humidityExternal");
$sql="SELECT ".implode(',',$queryFields)." FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 DAY)";
}

elseif ($dataRange === "Annual") {
$queryFields = array("sampledDate","decidegreesInternalHigh","decidegreesInternalLow","pressureInternalHigh","pressureInternalLow","humidityInternalHigh","humidityInternalLow", "decidegreesExternalHigh", "decidegreesExternalLow", "humidityExternalHigh", "humidityExternalLow", "voltageExternal1");
$sql = "SELECT ".implode(',',$queryFields)."  FROM dailyExtremes WHERE sampledDate > DATE_SUB(NOW(), INTERVAL 1 YEAR)";
}
$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

if (mysqli_num_rows($result)!=0)
{

    $tmpArray =mysqli_fetch_all($result,MYSQLI_ASSOC);
    $myArray = array();
    
    foreach ($queryFields as $fieldName)
    {
        $myArray[$fieldName] = array_column($tmpArray,$fieldName);
    }

} else {

    $myArray = "noData";

}

echo json_encode($myArray, JSON_NUMERIC_CHECK);
mysqli_close($conn);

?>
