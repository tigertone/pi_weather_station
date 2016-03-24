<!DOCTYPE html>
<html>
<head>
<style>
body {
    background-color: #008000;
    color: white;
    text-align: center;
    position: relative;
    top: 20px;
}
</style>
</head>

<body>

<?php
$servername = "localhost";
$username = "database_reader";
$password = "PASSWORD";
$dbname = "weather_records";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get most recent data
$sql = "SELECT GMT, decidegrees, pressure, humidity FROM sensor_data ORDER BY ID DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$tmp_data = mysqli_fetch_row($result);
mysqli_free_result($result);
$GMT_time = $tmp_data[0];
$decidegrees_current = $tmp_data[1];
$pressure_current = $tmp_data[2];
$humidity_current = $tmp_data[3];
$temp_current = $decidegrees_current / 10;

// Get the current days high/low values
$sql = "SELECT MAX(decidegrees), MIN(decidegrees), MAX(pressure), MIN(pressure), MAX(humidity), MIN(humidity) FROM sensor_data WHERE DATE(GMT) = CURDATE()";
$result = mysqli_query($conn, $sql);
$tmp_data = mysqli_fetch_row($result);
mysqli_free_result($result);
mysqli_close($conn);
$decidegrees_high = $tmp_data[0];
$decidegrees_low = $tmp_data[1];
$pressure_high = $tmp_data[2];
$pressure_low = $tmp_data[3];
$humidity_high = $tmp_data[4];
$humidity_low = $tmp_data[5];
$temp_high = $decidegrees_high / 10;
$temp_low = $decidegrees_low / 10;


echo "Last reading: " . $GMT_time . "<br><br>";

echo "Temp: " . $temp_current . "&degC<br>";
echo "(" . $temp_low . " - " . $temp_high . ")<br><br>";

echo "Pressure: " . $pressure_current . "mbar<br>";
echo "(" . $pressure_low . " - " . $pressure_high . ")<br><br>";

echo "Humidity: " . $humidity_current . "&#37<br>";
echo "(" . $humidity_low . " - " . $humidity_high . ")<br><br>";
?>  

</body>
</html>
