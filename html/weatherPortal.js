
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
        Chart.defaults.global.elements.line.spanGaps = false;
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

                        	var currentDate = new Date();
			var xAxesEnd = currentDate.toUTCString();
			var xAxesStart = new Date(currentDate.setDate(currentDate.getDate() - 1)).toUTCString();

                            xData = returnedData.GMT;

                            toPlot1 = ['decidegreesExternal', 'pressureNull',     'humidityExternal'];
   	                        toPlot2 = ['decidegreesInternal', 'pressureInternal', 'humidityInternal'];
   	                        legendStr = ['Temp (°C)',         'Pressure (mbar)',  'Humidity (%)'];
 
 data1Colour = 'rgba(15,170,10,0.5)';
data2Colour = 'rgba(150, 150, 150, 0.5)';

                document.getElementById("legendLabel1").textContent = "  External";
                document.getElementById("legendLabel2").textContent = "  Internal";
                                document.getElementById("legendLine1").style.borderColor = data1Colour;
                                document.getElementById("legendLine2").style.borderColor = data2Colour;

                            } else if (tabName == 'Annual') {

			var currentDate = new Date();
			var xAxesEnd = currentDate.toUTCString();
			var xAxesStart = new Date(currentDate.setFullYear(currentDate.getFullYear() - 1)).toUTCString();

	                                     xData = returnedData.sampledDate;

	                                     toPlot1 = ['decidegreesExternalHigh', 'decidegreesInternalHigh', 'humidityExternalHigh', 'humidityInternalHigh', 'pressureInternalHigh', 'voltageExternal1'];
   	                                     toPlot2 = ['decidegreesExternalLow',  'decidegreesInternalLow',  'humidityExternalLow',  'humidityInternalLow',  'pressureInternalLow',  ''];
   	                                     legendStr = ['External temp (°C)',    'Internal temp (°C)',      'External humidity (%)','Internal humidity (%)','Internal pressure (mbar)', 'External voltage (V)'];
        
data1Colour = 'rgba(255,40,40,0.5)';
data2Colour = 'rgba(30, 144, 255, 0.5)';

                document.getElementById("legendLabel1").textContent = "  High";
                document.getElementById("legendLabel2").textContent = "  Low";
                document.getElementById("legendLine1").style.borderColor = data1Colour;
                                document.getElementById("legendLine2").style.borderColor = data2Colour;


        }
                        
mydiv = document.getElementById('chartContainerID');
		    	
		    	if (mydiv) {
		    	while (mydiv.firstChild) {
		    	    mydiv.removeChild(mydiv.firstChild);	
		    	}
		    	}

				            for (i=0; i<toPlot1.length; i++) {
                                  

                                 var div = document.createElement('div');
				div.classList.add('lineChartContainer');
				var canvas = document.createElement('canvas');
				div.appendChild(canvas);
				document.querySelector('.chartContainer').appendChild(div);
				


                if (toPlot1[i].startsWith('pressure')) {
				        new Chart(canvas.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]], returnedData[toPlot2[i]], xAxesStart, xAxesEnd,  925, 1050, legendStr[i], data1Colour, data2Colour));
                } else if (toPlot1[i].startsWith('decidegrees')) {
                        new Chart(canvas.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]].map(function(x) { if (x!=null) {return x / 10} return null }), returnedData[toPlot2[i]].map(function(x) { if (x!=null) {return x / 10} return null }), xAxesStart, xAxesEnd,  -10, 30, legendStr[i], data1Colour, data2Colour));               
                } else if (toPlot1[i].startsWith('humidity')) {
                	    new Chart(canvas.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]], returnedData[toPlot2[i]], xAxesStart, xAxesEnd, 0, 100, legendStr[i], data1Colour, data2Colour));
                } else if (toPlot1[i].startsWith('voltage')) {
                	new Chart(canvas.getContext('2d'), createConfig(xData, returnedData[toPlot1[i]].map(function(x) { if (x!=null) {return x / 10} return null }), [], xAxesStart, xAxesEnd, 0, 4, legendStr[i], data1Colour, data2Colour));
                }             
                document.getElementById("footer").style.visibility = "visible";
                document.getElementById("legend").style.visibility = "visible";



				            }

	                           
	                                              
		    }

            }
	}

            xmlhttp.open("GET", "getData.php?dataRange=" + tabName + "&resamplingInterval=" + Math.ceil((60 * 24) / (window.innerWidth * .9)), true);
            xmlhttp.send();

        }


function createConfig(xData, yData1, yData2, xmin, xmax, ymin, ymax, yLabel, data1Colour, data2Colour) {

            config = {
                type: 'line',
                data: {
                    labels: xData,
                    datasets: [{
                            data: yData1,
                             borderColor: data1Colour,    
                        },

                        {
                            data: yData2,
                            borderColor: data2Colour,

                        },
                    ]
                },

                options: {

                    scales: {
                        xAxes: [{
                           type: "time",
                           time: {
                                max: xmax,
                                min: xmin
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMax: ymax,
                                suggestedMin: ymin
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
            



