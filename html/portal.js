
// Set properties common to all charts
Chart.defaults.plugins.legend.display = false;
Chart.defaults.plugins.tooltip.enabled = false;
Chart.defaults.scale.ticks.maxRotation = 0;
Chart.defaults.scale.ticks.minRotation = 0;
//Chart.defaults.maintainAspectRatio = false;
//Chart.defaults.responsive = true;
Chart.defaults.animation = false;
Chart.defaults.normalized = true;
Chart.defaults.parsed = true;
Chart.defaults.plugins.title.display = false;
Chart.defaults.elements.line.fill = false;
Chart.defaults.elements.line.borderJoinStyle = 'round';
Chart.defaults.elements.line.borderCapStyle = 'round';
Chart.defaults.elements.line.tension = 0;
Chart.defaults.elements.line.pointBorderWidth = 2;

Chart.overrides.line.spanGaps = false;
Chart.defaults.elements.point.radius = 0;
Chart.defaults.fontSize = 8;
Chart.defaults.events = [];

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

function selectTab(evt, tabName)
{
	if (typeof statusUpdateTimer !== "undefined")
	{
		clearInterval(statusUpdateTimer);
	}

	var i, tablinks;
	tablinks = document.getElementsByClassName("tablinks");

	for (i = 0; i < tablinks.length; i++)
	{
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}

	evt.currentTarget.className += " active";
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onload = function()
	{
			returnedData = JSON.parse(xmlhttp.responseText);

			if  (returnedData!='noData')
			{
				if (tabName == 'Current')
				{
					toPlot1 = [];

					document.getElementById("chartContainerID").style.display = "none";
					document.getElementById("currentTable").style.display = "table";
					document.getElementById("statusTable").style.display = "none";
					document.getElementById("currentExtTempNow").textContent = (returnedData.decidegreesExternal/10).toFixed(1) +"°C";
					document.getElementById("currentExtTempHigh").textContent = (returnedData.decidegreesExternalHigh/10).toFixed(1);
					document.getElementById("currentExtTempLow").textContent = (returnedData.decidegreesExternalLow/10).toFixed(1);
					document.getElementById("currentExtHumNow").textContent = returnedData.humidityExternal + "%";
					document.getElementById("currentExtHumLow").textContent = returnedData.humidityExternalLow;
					document.getElementById("currentExtHumHigh").textContent = returnedData.humidityExternalHigh;
					document.getElementById("currentIntTempNow").textContent = (returnedData.decidegreesInternal/10).toFixed(1) +"°C";
					document.getElementById("currentIntTempHigh").textContent = (returnedData.decidegreesInternalHigh/10).toFixed(1);
					document.getElementById("currentIntTempLow").textContent = (returnedData.decidegreesInternalLow/10).toFixed(1);
					document.getElementById("currentIntHumNow").textContent = returnedData.humidityInternal + "%";
					document.getElementById("currentIntHumLow").textContent = returnedData.humidityInternalLow;
					document.getElementById("currentIntHumHigh").textContent = returnedData.humidityInternalHigh;
					document.getElementById("currentIntPressNow").textContent = returnedData.pressureInternal + "mBar";
					document.getElementById("currentIntPressTrend").textContent = returnedData.pressureInternalTrend;

				}
				else if (tabName == 'Today')
				{
					currentDate = new Date();
					xAxesEnd = currentDate.getTime();
					xAxesStart = new Date(currentDate.setDate(currentDate.getDate() - 1));
					timeUnit = 'hour';

					xData = returnedData.GMT_timestamp;

					toPlot1 = ['decidegreesExternal', 'pressureNull',     'humidityExternal'];
					toPlot2 = ['decidegreesInternal', 'pressureInternal', 'humidityInternal'];
					legendStr = ['Temp (°C)',         'Pressure (mbar)',  'Humidity (%)'];

					data1Colour = 'rgba(15,170,10,1)';
					data2Colour = 'rgba(150, 150, 150, 1)';

				}
				else if (tabName == 'Annual')
				{

					currentDate = new Date();
					xAxesEnd = currentDate.getTime();
					xAxesStart = new Date(currentDate.setFullYear(currentDate.getFullYear() - 1)).getTime();

					timeUnit = 'month';

					xData = returnedData.sampledDate_timestamp;

					toPlot1 = ['decidegreesExternalHigh', 'decidegreesInternalHigh', 'humidityExternalHigh', 'humidityInternalHigh', 'pressureInternalHigh', 'voltageExternalTempSensor'];
					toPlot2 = ['decidegreesExternalLow',  'decidegreesInternalLow',  'humidityExternalLow',  'humidityInternalLow',  'pressureInternalLow',  ''];
					legendStr = ['External temp (°C)',    'Internal temp (°C)',      'External humidity (%)','Internal humidity (%)','Internal pressure (mbar)', 'External voltage (V)'];

					data1Colour = 'rgba(255,40,40,1)';
					data2Colour = 'rgba(30, 144, 255, 1)';

				}

				if (tabName == 'Status')
				{
					toPlot1 = [];

					document.getElementById("chartContainerID").style.display = "none";
					document.getElementById("currentTable").style.display = "none";
					document.getElementById("statusTable").style.display = "table";

					getStatus();
					statusUpdateTimer = setInterval(getStatus, 10000);

				}

				mydiv = document.getElementById('chartContainerID');

				while (mydiv.firstChild)
				{
					mydiv.removeChild(mydiv.firstChild);
				}


				for (i=0; i<toPlot1.length; i++)
				{

					if (i == 0)
					{
						document.getElementById("currentTable").style.display = "none";
						document.getElementById("statusTable").style.display = "none";
						document.getElementById("chartContainerID").style.display = "grid";
					}

					var div = document.createElement('div');
					div.className = "gridDiv";
					mydiv.appendChild(div);
					var canvas = document.createElement('canvas');
					div.appendChild(canvas);
				}



		for (i=0; i<toPlot1.length; i++)
				{
					if (toPlot1[i].startsWith('pressure'))
					{
						new Chart(mydiv.childNodes[i].firstChild.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]], returnedData[toPlot2[i]], xAxesStart, xAxesEnd, timeUnit, 925, 1050, legendStr[i], data1Colour, data2Colour));
					}
					else if (toPlot1[i].startsWith('decidegrees'))
					{
						new Chart(mydiv.childNodes[i].firstChild.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]].map(function(x) { if (x!=null) {return x / 10} return null }), returnedData[toPlot2[i]].map(function(x) { if (x!=null) {return x / 10} return null }), xAxesStart, xAxesEnd, timeUnit, -10, 30, legendStr[i], data1Colour, data2Colour));               
					}
					else if (toPlot1[i].startsWith('humidity'))
					{
						new Chart(mydiv.childNodes[i].firstChild.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]], returnedData[toPlot2[i]], xAxesStart, xAxesEnd, timeUnit, 0, 100, legendStr[i], data1Colour, data2Colour));
					}
					else if (toPlot1[i].startsWith('voltage'))
					{
						new Chart(mydiv.childNodes[i].firstChild.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]].map(function(x) { if (x!=null) {return x / 100} return null }), [], xAxesStart, xAxesEnd, timeUnit, 2, 5, legendStr[i], data1Colour, data2Colour));
					}
				}
			}
	}

	xmlhttp.open("GET", "getData.php?dataRange=" + tabName, true);
	xmlhttp.send();

}




function createConfig(xData, yData1, yData2, xmin, xmax, timeUnit, ymin, ymax, yLabel, data1Colour, data2Colour)
{

	config = 
	{
		type: 'line',
		data: 
		{
			labels: xData,
			datasets: 
			[
				{
					data: yData1,
					borderColor: data1Colour,
				},
		
				{
					data: yData2,
					borderColor: data2Colour,
		
				},
			]
		},
	
		options: 
		{
	
			scales:
			{
				x:
				{
					max:xmax,
					min:xmin,
					type: 'time',
                			time:
					{
						unit: timeUnit,
						displayFormats:
						{
							month: 'MMM',
						},
					},
				},
				y:
				{

					suggestedMax: ymax,
					suggestedMin: ymin,

					scaleLabel:
					{
						display: true,
						labelString: yLabel
					}
				},
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
			}
			else
			{
				document.getElementById("statusInternal").textContent = "No Data";
			}

			if (samplingStatus.externalSampling == 1)
			{
				document.getElementById("statusExternal").textContent = "OK";
			}
			else
			{
				document.getElementById("statusExternal").textContent = "No recent data";
			}
			if (samplingStatus.voltageExternalTempSensor == 0)
                        {
                                document.getElementById("statusVoltageExternal").textContent = "No Data";
                        }
                        else
                        {
				document.getElementById("statusVoltageExternal").textContent = (samplingStatus.voltageExternalTempSensor/100).toFixed(2) + "v";
                        }
			document.getElementById("statusPercentExternal").textContent = samplingStatus.percentSuccessTemp + "%";
			document.getElementById("statusSpaceMb").textContent = samplingStatus.spaceRemainingMb + "MB";
			document.getElementById("statusSpacePercent").textContent = samplingStatus.spaceRemainingPercent + "%";

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
