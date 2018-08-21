// Set properties common to all charts
        Chart.defaults.global.legend.display = false;
        Chart.defaults.global.tooltips.enabled = false;
        Chart.defaults.global.maintainAspectRatio = false;
        Chart.defaults.global.responsive = true;
        Chart.defaults.global.animation.duration = 0;
        Chart.defaults.global.title.display = false;
        //Chart.defaults.global.scales.xAxes.type = "time";
        //Chart.defaults.global.scales.xAxes.display: true;
        Chart.defaults.global.elements.line.fill = false;
        Chart.defaults.global.elements.line.tension = 0;
        //      Chart.defaults.line.showLines = false;
        Chart.defaults.global.elements.point.pointStyle = 'circle';
        Chart.defaults.global.elements.point.radius = 1;
        Chart.defaults.global.elements.point.borderWidth = 0;
        Chart.defaults.global.elements.line.borderWidth = 1;
        //    Chart.defaults.global.elements.point.borderColor = 'rgba(1,1,0,0)';
        Chart.defaults.global.defaultFontSize = 6;
        Chart.defaults.global.events = [];
        //    Chart.defaults.global.gridlines.display = false;
        var chartTemp;

        
        // Get the element with id="defaultOpen" and click on it
        document.getElementById("defaultOpen").click();


        setInterval(getStatus, 10000);


function selectTab(evt, tabName) {

            var i, tablinks;
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            //document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {;

                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

                    if (tabName == 'Today') {
                        returnedData = JSON.parse(xmlhttp.responseText);
                        GMT = returnedData.map(function(item) {
                            return item.GMT.toString()
                        });
                        temp = returnedData.map(function(item) {
                            return item.decidegrees.toString() / 10
                        });

                        pressure = returnedData.map(function(item) {
                            return item.pressure.toString()
                        });
                        humidity = returnedData.map(function(item) {
                            return item.humidity.toString()
                        });

                        if (typeof chartTemp == 'undefined') {
                            config = createConfig(GMT, temp, -10, 30, 'Temp (°C)')
                            ctx = document.getElementById('canvasTemp').getContext('2d');
                            chartTemp = new Chart(ctx, config)
                            config = createConfig(GMT, pressure, 925, 1050, 'Pressure (mbar)')
                            ctx = document.getElementById('canvasPressure').getContext('2d');
                            chartPressure = new Chart(ctx, config)
                            config = createConfig(GMT, humidity, 0, 100, 'Humidity (%)')
                            ctx = document.getElementById('canvasHumidity').getContext('2d');
                            chartHumidity = new Chart(ctx, config)
                            var footerElement = document.getElementById("footer");
                            footerElement.style.visibility = "visible";
                        } else {

                            chartTemp.data.datasets[0].data = temp;
                            chartTemp.data.datasets[1].data = [];
                            chartTemp.data.labels = GMT;
                            chartPressure.data.datasets[0].data = pressure;
                            chartPressure.data.datasets[1].data = [];
                            chartPressure.data.labels = GMT;
                            chartHumidity.data.datasets[0].data = humidity;
                            chartHumidity.data.datasets[1].data = [];
                            chartHumidity.data.labels = GMT;

                            chartTemp.data.datasets[0].backgroundColor = 'rgba(50, 50, 50, 0.5)';
                            chartPressure.data.datasets[0].backgroundColor = 'rgba(255, 0, 0, 0.5)';
                            chartHumidity.data.datasets[0].backgroundColor = 'rgba(255, 0, 0, 0.5)';



                        }
                    } else if (tabName == 'Annual') {
                        returnedData = JSON.parse(xmlhttp.responseText);
                        GMT = returnedData.map(function(item) {
                            return item.sampledDate.toString()
                        });
                        maxTemp = returnedData.map(function(item) {
                            return item.decidegreesHigh.toString() / 10
                        });

                        minTemp = returnedData.map(function(item) {
                            return item.decidegreesLow.toString() / 10
                        });
                        maxPressure = returnedData.map(function(item) {
                            return item.pressureHigh.toString()
                        });

                        minPressure = returnedData.map(function(item) {
                            return item.pressureLow.toString()
                        });
                        maxHumidity = returnedData.map(function(item) {
                            return item.humidityHigh.toString()
                        });

                        minHumidity = returnedData.map(function(item) {
                            return item.humidityLow.toString()
                        });

                        chartTemp.data.datasets[0].data = maxTemp;
                        chartTemp.data.datasets[1].data = minTemp;
                        chartTemp.data.labels = GMT;
                        chartPressure.data.datasets[0].data = maxPressure
                        chartPressure.data.datasets[1].data = minPressure
                        chartPressure.data.labels = GMT;
                        chartHumidity.data.datasets[0].data = maxHumidity;
                        chartHumidity.data.datasets[1].data = minHumidity;
                        chartHumidity.data.labels = GMT;
                        chartTemp.data.datasets[0].backgroundColor = 'rgba(255, 0, 0, 0.5)';
                        chartTemp.data.datasets[1].backgroundColor = 'rgba(0, 0, 255, 0.5)';
                        chartPressure.data.datasets[0].backgroundColor = 'rgba(255, 0, 0, 0.5)';
                        chartPressure.data.datasets[1].backgroundColor = 'rgba(0, 0, 255, 0.5)';
                        chartHumidity.data.datasets[0].backgroundColor = 'rgba(255, 0, 0, 0.5)';
                        chartHumidity.data.datasets[1].backgroundColor = 'rgba(0, 0, 255, 0.5)';
                    }

                    chartPressure.update();
                    chartTemp.update();
                    chartHumidity.update();
                };
            }

            xmlhttp.open("GET", "getData.php?dataRange=" + tabName + "&resamplingInterval=" + Math.ceil((60 * 24) / (window.innerWidth * .9)), true);
            xmlhttp.send();

        }


function createConfig(xData, yData, min, max, yLabel) {
            config = {
                type: 'line',
                data: {
                    labels: xData,
                    datasets: [{
                            data: yData
                        },

                        {
                            data: []

                        },
                    ]
                },

                options: {

                    scales: {
                        xAxes: [{
                            type: "time"
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMax: max,
                                suggestedMin: min
                            },
                            scaleLabel: {
                                display: true,
                                labelString: yLabel
                            }
                        }]
                    },
                }
            }
            return config
        }


function getStatus() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                var statusIconElement = document.getElementById("statusIcon");
                if (this.readyState == 4 && this.status == 200) {
                    if (xhttp.responseText == 1) {
                        statusIconElement.style.backgroundColor = "#94E185";
                        statusIconElement.style.boxShadow = "0px 0px 4px 1px #94E185";
                        statusIconElement.style.borderColor = "#78D965";
                    } else {
                        statusIconElement.style.backgroundColor = "#C9404D";
                        statusIconElement.style.boxShadow = "0px 0px 4px 1px #C9404D";
                        statusIconElement.style.borderColor = "#C42C3B";
                    }
                } else if (this.status == 0) {
                    statusIconElement.style.backgroundColor = "#FFC182";
                    statusIconElement.style.boxShadow = "0px 0px 4px 1px #FFC182";
                    statusIconElement.style.borderColor = "#FFB161";
                }
            }


            xhttp.open("GET", "getStatus.php", true);
            xhttp.send();
        }


