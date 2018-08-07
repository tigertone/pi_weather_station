<!DOCTYPE html>
<html>

<head>



</head>

<body>

    <?php $servername="localhost" ; $username="database_reader" ; $password="PASSWORD" ; $dbname="weather_records" ;
// Create connection
$conn=mysqli_connect($servername, $username, $password, $dbname); 

// Check connection
if (!$conn) { 
die( "Connection failed: " . mysqli_connect_error()); 
} 
// Get most recent data
// $sql="SELECT date_format(GMT,\" %T\ ") as GMT, decidegrees FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 DAY)";
$sql="SELECT GMT, decidegrees, pressure, humidity FROM sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 DAY) AND ID mod 100 = 0" ;
$result=mysqli_query($conn, $sql) or die(mysqli_error($conn)); 
if (mysqli_num_rows($result)> 0) {
$weatherData = (mysqli_fetch_all($result, MYSQLI_ASSOC));
// $temp = json_encode($weatherData);
$temp = json_encode(array_column($weatherData, 'decidegrees'));
$GMT = json_encode(array_column($weatherData, 'GMT'));
$pressure =json_encode(array_column($weatherData, 'pressure'));
$humidity = json_encode(array_column($weatherData, 'humidity'));
} 
else { echo "no results found"; }
mysqli_close($conn); ?>

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
        var temp = JSON.parse('<?php echo $temp; ?>');
        temp = temp.map(function(x) {
            return x / 10;
        });
        var GMT = JSON.parse('<?php echo $GMT; ?>');
        var humidity = JSON.parse('<?php echo $humidity; ?>');
        var pressure = JSON.parse('<?php echo $pressure; ?>');


        function createConfig(xData, yData) {
            return {};
        }

        window.onload = function() {

            var ctx = document.getElementById('canvasTemp').getContext('2d');
            config = {
                type: 'line',
                data: {
                    labels: GMT,
                    datasets: [{
                        data: temp,
                        fill: false,
                        showLine: false,
                        pointStyle: 'circle',
                        radius: 2,
                        borderWidth: 0,

                    }]
                },
                options: {
                    tooltips: {
                        enaled: false
                    },
                    responsive: true,
                    elements: {
                        line: {
                            tension: 0,
                        },
                    },
                    animation: {
                        duration: 0
                    },
                    scales: {
                        xAxes: [{
                            type: "time",
                            display: true,
                            labelString: 'Time',
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMax: 30,
                                suggestedMin: -10,
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
                        data: pressure,
                        fill: false,
                        showLine: false,
                        pointStyle: 'circle',
                        radius: 2,
                        borderWidth: 0,

                    }]
                },
                options: {
                    responsive: true,
                    elements: {
                        line: {
                            tension: 0,
                        },
                    },
                    animation: {
                        duration: 0
                    },
                    scales: {
                        xAxes: [{
                            type: "time",
                            display: true,
                            labelString: 'Time',
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMax: 1050,
                                suggestedMin: 925,
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
                        data: humidity,
                        fill: false,
                        showLine: false,
                        pointStyle: 'circle',
                        radius: 2,
                        borderWidth: 0,

                    }]
                },
                options: {
                    responsive: true,
                    elements: {
                        line: {
                            tension: 0,
                        },
                    },
                    animation: {
                        duration: 0
                    },
                    scales: {
                        xAxes: [{
                            type: "time",
                            display: true,
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMax: 100,
                                suggestedMin: 0,
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

    <a href="annualData.php">Annual data</a>


</body>

</html>

