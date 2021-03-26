<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 'on');
error_reporting(E_ALL);

$servername="localhost";
$username="database_reader";
$password="";
$dbname="weatherLog";


$dataOut = new stdClass();
$dataOut->spaceRemainingMb = intval((disk_free_space("/")/1e6) + 0.5);
$dataOut->spaceRemainingPercent = intval(($dataOut->spaceRemainingMb / (disk_total_space("/") /1e6) * 100) + 0.5);

// Create connection
$conn=mysqli_connect($servername, $username, $password, $dbname);



// Check connection
if (!$conn) {
die( "Connection failed: " . mysqli_connect_error());
}

$result_sensorData=mysqli_query($conn, "SELECT COUNT(decidegreesInternal) as decidegreesInternal, COUNT(decidegreesExternal) as decidegreesExternal FROM sensorData WHERE GMT > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 70 SECOND)") or die(mysqli_error($conn));
$result_percentSuccessTemp=mysqli_query($conn, "SELECT ROUND(COUNT(decidegreesExternal)/60*100,0) as percentSuccess FROM sensorData WHERE GMT > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 HOUR)") or die(mysqli_error($conn));

$result_voltageExternalTempSensor=mysqli_query($conn, "SELECT voltageExternalTempSensor FROM dailyExtremes WHERE voltageExternalTempSensor IS NOT NULL ORDER BY id desc LIMIT 1") or die(mysqli_error($conn));

    if (mysqli_num_rows($result_voltageExternalTempSensor)!=0)
	{
	    $voltageExternalTempSensor = mysqli_fetch_all($result_voltageExternalTempSensor,MYSQLI_ASSOC);
	    $dataOut->voltageExternalTempSensor = $voltageExternalTempSensor[0]['voltageExternalTempSensor'];
	}
	else
	{
	    $dataOut->voltageExternalTempSensor = 0;
	}



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


echo json_encode($dataOut, JSON_NUMERIC_CHECK);
//echo $externalSampling;
//echo $percentSuccessTemp[0]['percentSuccess'];
//echo $voltage1[0]['voltageExternal1'];

mysqli_close($conn);

?>
