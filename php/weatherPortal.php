<!DOCTYPE html>
<html>
  <head>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      body {
        font-family: Arial;
        margin:0;
        padding:0;
      }
      .container {
        display: grid;
        position: relative;
        grid-template-columns: repeat(auto-fill, minmax(200px, 100vw));
        grid-template-rows: repeat(minmax(20vh, 40vh));
      }
      /* Style the tab */
      .tab {
        position: fixed;
        width:100%;
        overflow: hidden;
        top: 0;
        left:0;
        z-index: 10;
        background-color: #f1f1f1;
      }
      .spacer{
        width:100%;
        height:75px;
      }
      /* Style the buttons inside the tab */
      .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
      }
      /* Change background color of buttons on hover */
      .tab button:hover {
        background-color: #ddd;
      }
      /* Create an active/current tablink class */
      .tab button.active {
        background-color: #ccc;
      }
    </style>
    <div class="tab">
      <button class="tablinks" onclick="selectTab(event, 'Today')" id="defaultOpen">Today
      </button>
      <button class="tablinks" onclick="selectTab(event, 'Annual')">Annual
      </button>
    </div>
    <div class="spacer">
    </div>
  </head>
  <body>

    <div class="container">
      <div>
        <canvas id="canvasTemp" position="absolute">
        </canvas>
      </div>
      <div>
        <canvas id="canvasPressure" position="absolute">
        </canvas>
      </div>
      <div>
        <canvas id="canvasHumidity" position="absolute">
        </canvas>
      </div>
    </div>
    <script src="/Chart.bundle.min.js">
    </script>
    <script>
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
      Chart.defaults.global.elements.point.radius =1;
      Chart.defaults.global.elements.point.borderWidth = 0;
      Chart.defaults.global.elements.line.borderWidth =1;
  //    Chart.defaults.global.elements.point.borderColor = 'rgba(1,1,0,0)';
      Chart.defaults.global.defaultFontSize = 6;
      Chart.defaults.global.events = [];
  //    Chart.defaults.global.gridlines.display = false;
      var chartTemp;

      function createConfig(xData, yData, min, max, yLabel){
        config = {
          type: 'line',
          data: {
            labels: xData,
            datasets: [{
              data: yData
            },

{  data: []
            
            }                                                                                                                     ,
                     ]
},

 options: {
          
            scales: {
              xAxes:[{
                type: "time" }],
              yAxes: [{
                ticks: {
                  suggestedMax: max,
                  suggestedMin: min
                }
                ,
               scaleLabel: {
                  display: true,
                  labelString: yLabel
                }
              }
                     ]
            }
            ,
          }
        }
        return config
      }



      function	selectTab(evt, tabName) {

	var i, tablinks;
       	 	tablinks = document.getElementsByClassName("tablinks");
        	for (i = 0; i < tablinks.length; i++) {
         	   tablinks[i].className = tablinks[i].className.replace(" active", "");
        	}

//document.getElementById(tabName).style.display = "block";
        	evt.currentTarget.className += " active";
      		xmlhttp = new XMLHttpRequest(); 
      		xmlhttp.onreadystatechange = function(){;

	 		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

if(tabName == 'Today'){
               		    returnedData = JSON.parse(xmlhttp.responseText);
			    GMT = returnedData.map(function(item){
				return item.GMT.toString()
			   });
temp = returnedData.map(function(item){
                                return item.decidegrees.toString() / 10
                            });

pressure = returnedData.map(function(item){
                                return item.pressure.toString()
                            });
humidity = returnedData.map(function(item){
                                return item.humidity.toString()
                            });

if (typeof chartTemp == 'undefined') {
config = createConfig(GMT,temp, -10, 30, 'Temp (°C)')
        ctx = document.getElementById('canvasTemp').getContext('2d');
        chartTemp = new Chart(ctx, config)
        config = createConfig(GMT,pressure, 925, 1050, 'Pressure (mbar)')
        ctx = document.getElementById('canvasPressure').getContext('2d');
        chartPressure = new Chart(ctx, config)
        config = createConfig(GMT,humidity, 0, 100, 'Humidity (%)')
        ctx = document.getElementById('canvasHumidity').getContext('2d');
        chartHumidity = new Chart(ctx, config)
}
else {

chartTemp.data.datasets[0].data = temp;
chartTemp.data.datasets[1].data = [];
chartTemp.data.labels = GMT;
 chartPressure.data.datasets[0].data=pressure 
chartPressure.data.datasets[1].data = []; 
chartPressure.data.labels =GMT; 
chartHumidity.data.datasets[0].data = humidity;
chartHumidity.data.datasets[1].data = []; 
chartHumidity.data.labels = GMT;

chartTemp.data.datasets[0].backgroundColor ='rgba(50, 50, 50, 0.5)';
chartPressure.data.datasets[0].backgroundColor ='rgba(255, 0, 0, 0.5)';  
chartHumidity.data.datasets[0].backgroundColor ='rgba(255, 0, 0, 0.5)';



}
}
else if(tabName=='Annual') {
               		    returnedData = JSON.parse(xmlhttp.responseText);
			    GMT = returnedData.map(function(item){
				return item.sampledDate.toString()
			   });
maxTemp = returnedData.map(function(item){
                                return item.decidegreesHigh.toString() / 10
                            });

minTemp = returnedData.map(function(item){
                                return item.decidegreesLow.toString() / 10
                            });
maxPressure = returnedData.map(function(item){
                                return item.pressureHigh.toString()
                            });

minPressure = returnedData.map(function(item){
                                return item.pressureLow.toString()
                            });
maxHumidity = returnedData.map(function(item){
                                return item.humidityHigh.toString()
                            });

minHumidity = returnedData.map(function(item){
                                return item.humidityLow.toString()
                            });

chartTemp.data.datasets[0].data = maxTemp;
chartTemp.data.datasets[1].data = minTemp;
chartTemp.data.labels = GMT;
 chartPressure.data.datasets[0].data=maxPressure 
 chartPressure.data.datasets[1].data=minPressure
chartPressure.data.labels =GMT; 
chartHumidity.data.datasets[0].data = maxHumidity;
chartHumidity.data.datasets[1].data = minHumidity;
chartHumidity.data.labels = GMT;
chartTemp.data.datasets[0].backgroundColor ='rgba(255, 0, 0, 0.5)';
chartTemp.data.datasets[1].backgroundColor ='rgba(0, 0, 255, 0.5)';
chartPressure.data.datasets[0].backgroundColor ='rgba(255, 0, 0, 0.5)';
chartPressure.data.datasets[1].backgroundColor ='rgba(0, 0, 255, 0.5)';
chartHumidity.data.datasets[0].backgroundColor ='rgba(255, 0, 0, 0.5)';
chartHumidity.data.datasets[1].backgroundColor ='rgba(0, 0, 255, 0.5)';
}

chartPressure.update();
chartTemp.update();
chartHumidity.update();
		};
}

		xmlhttp.open("GET","getData.php?q="+tabName, true);
        	xmlhttp.send();

}



// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();


</script> 
  </body> 
</html> 
