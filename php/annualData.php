
<!DOCTYPE html>
<html>
<head>



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
// $sql = "SELECT date_format(GMT,\"%T\") as GMT, decidegrees FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 DAY)";
$time_start = microtime(true);
echo (microtime(true)-$time_start);
echo PHP_EOL;
$sql = "select date(GMT) as day, max(decidegrees) as maxTemp, MIN(decidegrees) as minTemp, max(Pressure) as maxPressure, MIN(Pressure) as minPressure, max(Humidity) as maxHumidity, MIN(Humidity) as minHumidity from sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 YEAR) group by day";
$result = mysqli_query($conn, $sql)  or die(mysqli_error($conn));
echo (microtime(true)-$time_start);
echo PHP_EOL;
if (mysqli_num_rows($result) > 0)
{
   $weatherData = (mysqli_fetch_all($result, MYSQLI_ASSOC));
//   $temp = json_encode($weatherData);

   $maxTemp = json_encode(array_column($weatherData, 'maxTemp'));
   $minTemp = json_encode(array_column($weatherData, 'minTemp'));
   $GMT = json_encode(array_column($weatherData, 'day'));
   $maxPressure = json_encode(array_column($weatherData, 'maxPressure'));
   $minPressure = json_encode(array_column($weatherData, 'minPressure'));
   $maxHumidity = json_encode(array_column($weatherData, 'maxHumidity'));
   $minHumidity = json_encode(array_column($weatherData, 'minHumidity'));

//   $pressure =json_encode(array_column($weatherData, 'pressure'));
//   $humidity = json_encode(array_column($weatherData, 'humidity'));
}
else
{
    echo "no results found";
}


mysqli_close($conn);
echo (microtime(true)-$time_start);  
echo PHP_EOL;
?>

<div>
  <canvas id="canvasTemp" width="500" height="200"></canvas>
</div>
<div>
  <canvas id="canvasPressure" width="500" height="200"></canvas>
</div>
<div>
  <canvas id="canvasHumidity" width="500" height="200"></canvas>
</div>





<script src="/Chart.bundle.min.js">

</script>



  <script>



    Chart.defaults.global.legend.display = false;
   // Chart.defaults.global.tooltips.enabled = false;
    var maxTemp = JSON.parse( '<?php echo $maxTemp; ?>' );
    maxTemp = maxTemp.map(function(x){return x / 10;});
   var minTemp = JSON.parse( '<?php echo $minTemp; ?>' );
    minTemp = minTemp.map(function(x){return x / 10;});
    var GMT = JSON.parse( '<?php echo $GMT; ?>' );
    var maxPressure = JSON.parse( '<?php echo $maxPressure; ?>' );
    var minPressure = JSON.parse( '<?php echo $minPressure; ?>' );
    var maxHumidity = JSON.parse( '<?php echo $maxHumidity; ?>' );
    var minHumidity = JSON.parse( '<?php echo $minHumidity; ?>' );


 
       function createConfig(xData, yData) {
            return {
            };

        }

        window.onload = function() {

                var ctx = document.getElementById('canvasTemp').getContext('2d');
config = {
                type: 'line',
                data: {
                    labels: GMT,
                    datasets: [{
                        data: maxTemp,
fill: false,
        showLine: false,
pointStyle: 'circle',
radius: 2,
borderWidth:0,
backgroundColor:'rgba(255, 0, 0, 0.5)', 
                    },

{
data: minTemp,
fill: false,
        showLine: false,
pointStyle: 'circle',
radius: 2,
borderWidth:0,
backgroundColor:'rgba(0, 0, 255, 0.5)', 
                    }


]
                },
                options: {
                    responsive: true,
elements:{
line:{
tension:0,
},
},

animation: {duration: 0},
                    scales: {
                        xAxes: [{
			    type: "time",
                            display: true,
			    labelString: 'Time',
                        }],
                        yAxes: [{
ticks:{
suggestedMax:30,
suggestedMin:-10,
},

                                                scaleLabel: {
                                                        display: true,
                                                        labelString: 'Temp'
                                                }

}]

                    },
                    title: {
                        display: false,
                        }
                }
}
                new Chart(ctx, config)
		var ctx = document.getElementById('canvasPressure').getContext('2d');
config = {
                type: 'line',
                data: {
                    labels: GMT,
                    datasets: [{
                        data: maxPressure,
fill: false,
        showLine: false,
pointStyle: 'circle',
radius: 2,
borderWidth:0,
backgroundColor:'rgba(255, 0, 0, 0.5)', 
                    },

{
data: minPressure,
fill: false,
        showLine: false,
pointStyle: 'circle',
radius: 2,
borderWidth:0,
backgroundColor:'rgba(0, 0, 255, 0.5)', 
                    }


]

                },
                options: {
                    responsive: true,
elements:{
line:{
tension:0,
},
},
animation: {duration: 0},
                    scales: {
                        xAxes: [{
			    type: "time",
                            display: true,
			   labelString: 'Time',
                        }],
                        yAxes: [{
ticks:{
suggestedMax:1050,
suggestedMin:925,
},

                                                scaleLabel: {
                                                        display: true,
                                                        labelString: 'Pressure'
                                                }
                                        }]

                    },
                    title: {
                        display: false,
                        }
                }
}
new Chart(ctx, config)
		var ctx = document.getElementById('canvasHumidity').getContext('2d');
config = {
                type: 'line',
                data: {
                    labels: GMT,
                    datasets: [{
                        data: maxHumidity,
fill: false,
        showLine: false,
pointStyle: 'circle',
radius: 2,
borderWidth:0,
backgroundColor:'rgba(255, 0, 0, 0.5)', 
                    },

{
data: minHumidity,
fill: false,
        showLine: false,
pointStyle: 'circle',
radius: 2,
borderWidth:0,
backgroundColor:'rgba(0, 0, 255, 0.5)', 
                    }


]

                },
                options: {
                    responsive: true,
elements:{
line:{
tension:0,
},
},
animation: {duration: 0},
                    scales: {
                        xAxes: [{
			    type: "time",
                            display: true,
                        }],
                        yAxes: [{
ticks:{
suggestedMax:100,
suggestedMin:0,
},

                                                scaleLabel: {
                                                        display: true,
                                                        labelString: 'Humidity'
                                                }
                                        }]

                    },
                    title: {
                        display: false,
                        }
                }
}
new Chart(ctx, config)


};


  </script>

<a href="currentData.php">Current data</a>


 </body> </html>
