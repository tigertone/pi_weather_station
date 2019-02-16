<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername="localhost";
$username="database_reader";
$password="PASSWORD";
$dbname="weather_records";


$dataOut = new stdClass();

// Create connection
$conn=mysqli_connect($servername, $username, $password, $dbname);



// Check connection
if (!$conn) {
die( "Connection failed: " . mysqli_connect_error());
}

$result_sensorData=mysqli_query($conn, "SELECT COUNT(decidegreesInternal) as decidegreesInternal, COUNT(decidegreesExternal) as decidegreesExternal FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 70 SECOND)") or die(mysqli_error($conn));
$result_percentSuccessTemp=mysqli_query($conn, "SELECT ROUND(COUNT(decidegreesExternal)/60*100,0) as percentSuccess FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 HOUR)") or die(mysqli_error($conn));
$result_voltage1=mysqli_query($conn, "SELECT (voltageExternal1) as voltageExternal1 FROM dailyExtremes WHERE voltageExternal1 IS NOT NULL ORDER BY id desc LIMIT 1") or die(mysqli_error($conn));

if (mysqli_num_rows($result_sensorData)!=0)
{

    $result_sensorData = mysqli_fetch_all($result_sensorData,MYSQLI_ASSOC);

    if ($result_sensorData[0]['decidegreesInternal'] == 0)
    {
        $dataOut->externalSampling = 0;

    } else
    {
        $dataOut->internalSampling = 1;
    }

    if ($result_sensorData[0]['decidegreesExternal'] == 0)
    {
        $dataOut->externalSampling = 0;
    } else
    {
        $dataOut->externalSampling = 1;
    }


} else {

        $dataOut->internalSampling = 0;
        $dataOut->externalSampling = 0;


}

if (mysqli_num_rows($result_percentSuccessTemp)!=0)
{

    $percentSuccessTemp = mysqli_fetch_all($result_percentSuccessTemp,MYSQLI_ASSOC);
    $dataOut->percentSuccessTemp = $percentSuccessTemp[0]['percentSuccess'];
    
}
else
{
	$dataOut->percentSuccessTemp = NULL;
}

if (mysqli_num_rows($result_voltage1)!=0)
{

    $voltage1 = mysqli_fetch_all($result_voltage1,MYSQLI_ASSOC);
    $dataOut->voltage1 = $voltage1[0]['voltageExternal1'];
    
}
else
{
    $dataOut->voltage1 = NULL;
}


echo json_encode($dataOut, JSON_NUMERIC_CHECK);
//echo $externalSampling;
//echo $percentSuccessTemp[0]['percentSuccess'];
//echo $voltage1[0]['voltageExternal1'];

mysqli_close($conn);

?>
