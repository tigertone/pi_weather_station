<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//$startTime = microtime(true);
$dataRange=$_GET["dataRange"];

// Create connection
$conn=mysqli_connect("localhost", "database_reader","","weatherLog");



// Check connection
if (!$conn) {
die( "Connection failed: " . mysqli_connect_error());
}

//$queryFieldsInternal = array("GMT", "decidegreesInternal", "pressureInternal", "humidityInternal");
$queryFieldsInternal = array("ROUND(UNIX_TIMESTAMP(GMT)*1000) AS GMT_timestamp", "decidegreesInternal", "pressureInternal", "humidityInternal");
$queryFieldsExternal = array("decidegreesExternal", "humidityExternal");
$queryFieldsExtremes = array("ROUND(UNIX_TIMESTAMP(sampledDate)*1000) AS sampledDate_timestamp","decidegreesInternalHigh","decidegreesInternalLow","pressureInternalHigh","pressureInternalLow","humidityInternalHigh","humidityInternalLow", "decidegreesExternalHigh", "decidegreesExternalLow", "humidityExternalHigh", "humidityExternalLow", "voltageExternalTempSensor");

// Get most recent data
if ($dataRange === "Current")
{
	$queryFieldsAll = array_merge($queryFieldsInternal,$queryFieldsExternal, $queryFieldsExtremes, array("pressureInternalTrend"));
	$sqlQueries = array("SELECT ".implode(',',$queryFieldsInternal)." FROM sensorData WHERE (GMT > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 10 MINUTE) AND decidegreesInternal IS NOT NULL) order by ID desc limit 1");
	$sqlQueries[] ="SELECT ".implode(',',$queryFieldsExternal)." FROM sensorData WHERE (GMT > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 10 MINUTE) AND decidegreesExternal IS NOT NULL) order by ID desc limit 1";
	$sqlQueries[] ="SELECT ".implode(',',$queryFieldsExtremes)."  FROM dailyExtremes WHERE sampledDate = UTC_DATE()";
	$sqlQueries[] ="SELECT IF((SELECT pressureInternal FROM sensorData order by ID desc limit 1)>AVG(pressureInternal)+1,'Rising',IF((SELECT pressureInternal FROM sensorData order by ID desc limit 1)<AVG(pressureInternal)-1,'Falling','Settled')) as pressureInternalTrend FROM sensorData WHERE GMT > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 6 HOUR)";
}

elseif ($dataRange === "Today")
{
	$queryFieldsAll = array_merge($queryFieldsInternal,$queryFieldsExternal);
	$sqlQueries=array("SELECT ".implode(',',$queryFieldsAll)." FROM sensorData WHERE GMT > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)");
}

elseif ($dataRange === "Annual")
{
	$queryFieldsAll = $queryFieldsExtremes;
	$sqlQueries = array("SELECT ".implode(',',$queryFieldsAll)."  FROM dailyExtremes WHERE sampledDate > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 YEAR)");
}

$myArray= array();


$queryFieldsAll = str_replace("ROUND(UNIX_TIMESTAMP(GMT)*1000) AS GMT_timestamp","GMT_timestamp",$queryFieldsAll);
$queryFieldsAll = str_replace("ROUND(UNIX_TIMESTAMP(sampledDate)*1000) AS sampledDate_timestamp","sampledDate_timestamp",$queryFieldsAll);


foreach ($sqlQueries as $query)
{
	$result=mysqli_query($conn, $query);

	if (mysqli_num_rows($result)!=0)
	{

		$tmpArray =mysqli_fetch_all($result,MYSQLI_ASSOC);

		foreach ($queryFieldsAll as $fieldName)
		{
			if (!empty(array_column($tmpArray,$fieldName)))
			{
				$myArray[$fieldName] = array_column($tmpArray,$fieldName);
			}
		}
	}
}

foreach ($queryFieldsAll as $fieldName)
		{
			if (!empty(array_column($myArray,$fieldName)))
			{
				$myArray[$fieldName] = NULL;
			}
		}


//echo microtime(true) - $startTime;
echo json_encode($myArray, JSON_NUMERIC_CHECK);

mysqli_close($conn);

?>
