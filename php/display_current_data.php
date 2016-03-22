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

# $currentDate = date("Y-m-d H:i:s")
# $startDate = date_sub($currentDate, DateInterval('24h'));
$sql = "SELECT decidegrees FROM sensor_data ORDER BY ID DESC LIMIT 1";
$tmp_result = mysqli_query($conn, $sql);
$decidegrees = mysqli_fetch_row($tmp_result)
mysqli_free_result($tmp_result);
mysqli_close($conn);
$temp = $decidegrees[0] / 10;
echo "Temp: " . $temp. &degC. "<br>";
?>  

</body>
</html>
