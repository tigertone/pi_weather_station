
// Set properties common to all charts

        Chart.defaults.global.legend.display = false;
        Chart.defaults.global.tooltips.enabled = false;
        Chart.defaults.global.maintainAspectRatio = false;
        Chart.defaults.global.responsive = true;
        Chart.defaults.global.animation.duration = 0;
        Chart.defaults.global.title.display = false;
	//Chart.defaults.global.scales.tickMarkLength = 0;
        //Chart.defaults.global.scales.xAxes.type = "time";
        //Chart.defaults.global.scales.xAxes.display: true;
        Chart.defaults.global.elements.line.fill = false;
        Chart.defaults.global.elements.line.borderJoinStyle = 'round';
        Chart.defaults.global.elements.line.tension = 0;
        Chart.defaults.global.elements.line.borderWidth = 2;

        //      Chart.defaults.line.showLines = false;
        Chart.defaults.global.elements.point.radius = 0;
        Chart.defaults.global.defaultFontSize = 6;
        Chart.defaults.global.events = [];
        //    Chart.defaults.global.gridlines.display = false;

        // Get the element with id="defaultOpen" and click on it
        document.getElementById("defaultOpen").click();

	getStatus();
        setInterval(getStatus, 10000);

                                config = createConfig([], [], [], -10, 30, 'Temp (°C)')
                            	ctx = document.getElementById('lineChart1').getContext('2d');
                            	chartTemp = new Chart(ctx, config)
                            	config = createConfig([], [], [], 925, 1050, 'Pressure (mbar)')
                            	ctx = document.getElementById('lineChart2').getContext('2d');
                            	chartPressure = new Chart(ctx, config)
                            	config = createConfig([], [], [], 0, 100, 'Humidity (%)')
                            	ctx = document.getElementById('lineChart3').getContext('2d');
                            	chartHumidity = new Chart(ctx, config)
                            	document.getElementById("footer").style.visibility = "visible";


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

		  returnedData = JSON.parse(xmlhttp.responseText);

		    if  (returnedData!='noData'){


                        if (tabName == 'Today') {

	                            chartTemp.data.datasets[0].data = returnedData.decidegreesInternal.map(function(x) { return x / 10; });
	                            chartTemp.data.datasets[1].data = returnedData.decidegreesExternal.map(function(x) { return x / 10; });
	                            chartTemp.data.labels = returnedData.GMT;
	                            chartPressure.data.datasets[0].data = returnedData.pressureInternal;
	                            chartPressure.data.datasets[1].data = [];
	                            chartPressure.data.labels = returnedData.GMT;
	                            chartHumidity.data.datasets[0].data = returnedData.humidityInternal;
	                            chartHumidity.data.datasets[1].data = returnedData.humidityExternal;
	                            chartHumidity.data.labels = returnedData.GMT;

	                            chartTemp.data.datasets[0].borderColor = 'rgba(255,40,40,0.5)';
                        chartTemp.data.datasets[1].borderColor = 'rgba(30, 144, 255, 0.5)';
                        chartPressure.data.datasets[0].borderColor = 'rgba(255,40,40,0.5)';
                        chartPressure.data.datasets[1].borderColor = 'rgba(30, 144, 255, 0.5)';
                        chartHumidity.data.datasets[0].borderColor = 'rgba(255,40,40,0.5)';
                        chartHumidity.data.datasets[1].borderColor = 'rgba(30, 144, 255, 0.5)';



			var currentDate = new Date();
			var xAxesEnd = currentDate.toUTCString();
			var xAxesStart = new Date(currentDate.setDate(currentDate.getDate() - 1)).toUTCString();



                    } else if (tabName == 'Annual') {

			var currentDate = new Date();
			var xAxesEnd = currentDate.toUTCString();
			var xAxesStart = new Date(currentDate.setFullYear(currentDate.getFullYear() - 1)).toUTCString();

			chartTemp.data.datasets[0].data = returnedData.decidegreesInternalHigh.map(function(x) { return x / 10; });
                        chartTemp.data.datasets[1].data = returnedData.decidegreesInternalLow.map(function(x) { return x / 10; });
                        chartTemp.data.labels = returnedData.sampledDate;
                        chartPressure.data.datasets[0].data = returnedData.pressureInternalHigh;
                        chartPressure.data.datasets[1].data = returnedData.pressureInternalLow;
                        chartPressure.data.labels = returnedData.sampledDate;
                        chartHumidity.data.datasets[0].data = returnedData.humidityInternalHigh;
                        chartHumidity.data.datasets[1].data = returnedData.humidityInternalLow;
                        chartHumidity.data.labels = returnedData.sampledDate;
                        chartTemp.data.datasets[0].borderColor = 'rgba(255,40,40,0.5)';
                        chartTemp.data.datasets[1].borderColor = 'rgba(30, 144, 255, 0.5)';
                        chartPressure.data.datasets[0].borderColor = 'rgba(255,40,40,0.5)';
                        chartPressure.data.datasets[1].borderColor = 'rgba(30, 144, 255, 0.5)';
                        chartHumidity.data.datasets[0].borderColor = 'rgba(255,40,40,0.5)';
                        chartHumidity.data.datasets[1].borderColor = 'rgba(30, 144, 255, 0.5)';
                    }

			chartTemp.options.scales.xAxes[0].time.min = xAxesStart;
			chartTemp.options.scales.xAxes[0].time.max = xAxesEnd;
			chartPressure.options.scales.xAxes[0].time.min = xAxesStart;
			chartPressure.options.scales.xAxes[0].time.max = xAxesEnd;
			chartHumidity.options.scales.xAxes[0].time.min = xAxesStart;
			chartHumidity.options.scales.xAxes[0].time.max = xAxesEnd;	
                    chartPressure.update();
                    chartTemp.update();
                    chartHumidity.update();
		    }

            }
	}

            xmlhttp.open("GET", "getData.php?dataRange=" + tabName + "&resamplingInterval=" + Math.ceil((60 * 24) / (window.innerWidth * .9)), true);
            xmlhttp.send();

        }


function createConfig(xData, yData1, yData2, min, max, yLabel) {
            config = {
                type: 'line',
                data: {
                    labels: xData,
                    datasets: [{
                            data: yData1
                        },

                        {
                            data: yData2

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


function getStatus()
{
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function()
            {
                if (this.readyState == 4 && this.status == 200) 
                {
                    samplingStatus = JSON.parse(xhttp.responseText);
                    if (samplingStatus.internalSampling == 1)
                    {
			document.getElementById("statusInternal").textContent = "OK";
//                        statusIconInternalElement.style.backgroundColor = "#94E185";
                    }
                    else if (samplingStatus.internalSampling == 0)
                    {
			document.getElementById("statusInternal").textContent = "OK";
//                        statusIconInternalElement.style.backgroundColor = "#C9404D";
                    }

                    if (samplingStatus.externalSampling == 1)
                    {
			document.getElementById("statusExternal").textContent = "OK";
//                        statusIconExternalElement.style.backgroundColor = "#94E185";
                    }
                    else if (samplingStatus.externalSampling == 0)
                    {
			document.getElementById("statusExternal").textContent = "No recent data";
//                        statusIconExternalElement.style.backgroundColor = "#C9404D";
                    }
		        document.getElementById("statusVoltageExternal").textContent = (samplingStatus.voltage1/10) + "v";
                	document.getElementById("statusPercentExternal").textContent = samplingStatus.percentSuccessTemp + "%";

		    }
		    else if (this.status == 404)
		    {
		        document.getElementById("statusInternal").textContent="No connection";
		        document.getElementById("statusExternal").textContent = "No connection";
                    document.getElementById("statusVoltageExternal").textContent = "-";
                	document.getElementById("statusPercentExternal").textContent = "-";
                }
            }


            xhttp.open("GET", "getStatus.php?" + (new Date()).getTime(), true);
            xhttp.send();
            }
            



