<!DOCTYPE html>
<html manifest="weatherPortal.appcache">




<head>

<link rel="manifest" href="/manifest.json">
<link rel="stylesheet" type="text/css" href="weatherPortal.css"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="apple-touch-icon" sizes="180x180" href="icon_180x180.png">
<link rel="apple-touch-icon" sizes="152x152" href="icon_152x152.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon_16x16.png" >  
<link rel="icon" type="image/png" sizes="32x32" href="favicon_32x32.png" >  


<meta name="viewport" content="width=device-width, initial-scale=1">


    <div class="tab">
        <button class="tablinks" onclick="selectTab(event, 'Current')" id="defaultOpen">Current
        </button>
        <button class="tablinks" onclick="selectTab(event, 'Today')">Today
        </button>
        <button class="tablinks" onclick="selectTab(event, 'Annual')">Annual
        </button>
        <button class="tablinks" onclick="selectTab(event, 'Status')">Status
        </button>
    </div>
</head>

<body>

    <div class="chartContainer" id="chartContainerID">
	</div>
	
	<table id="currentData" style="display: table; margin:0 auto; padding:0">
		<tr>
			<td colspan ="5"><img src="icon_180x180.png" alt="Outdoor" style="display:block; margin-left:auto; margin-right:auto;height:2.5em; padding:1.5em"></td>
		</tr>
		<tr>
			<td style="text-align:right">Temp: </td>
			<td style="text-align:left"><span id="currentExtTempNow"></span></td>
			<td style="text-align:right">(<span id="currentExtTempLow"></span></td>
			<td style="text-align:center"> | </td>
			<td style="text-align:left"><span id="currentExtTempHigh"></span>)</td>
		</tr>
		<tr>
			<td style="text-align:right">Humidity: </td>
			<td style="text-align:left"><span id="currentExtHumNow"></span></td>
			<td style="text-align:right">(<span id="currentExtHumLow"></span></td>
			<td style="text-align:center"> | </td>
			<td style="text-align:left"><span id="currentExtHumHigh"></span>)</td>
		</tr>
		<tr>
			<td colspan ="5"><img src="icon_180x180.png" alt="Outdoor" style="display:block; margin-left:auto; margin-right:auto;height:2.5em; padding:1.5em"></td>
		</tr>
		<tr>
			<td style="text-align:right">Temp: </td>
			<td style="text-align:left"><span id="currentIntTempNow"></span></td>	
			<td style="text-align:right">(<span id="currentIntTempLow"></span></td>
			<td style="text-align:center"> | </td>
			<td style="text-align:left"><span id="currentIntTempHigh"></span>)</td></tr>
		<tr>
			<td style="text-align:right">Humidity: </td>
			<td style="text-align:left"><span id="currentIntHumNow"></span></td>		
			<td style="text-align:right">(<span id="currentIntHumLow"></span></td>
			<td style="text-align:center"> | </td>
			<td style="text-align:left"><span id="currentIntHumHigh"></span>)</td></tr>
		<tr>
			<td style="text-align:right">Pressure: </td>
			<td style="text-align:left"><span id="currentIntPressNow"></span></td>
			<td colspan ="3" style="text-align:center">(<span id="currentIntPressTrend"></span>)</td>
		</tr>
		</table>
		

    <div id="status">

        Internal status: <span id="statusInternal"></span><br/>
        External status: <span id="statusExternal"></span><br/>
		Battery: <span id="statusVoltageExternal"></span><br/>
        External success rate: <span id="statusPercentExternal"></span><br/>

    </div>

    <script src="/chartJs_3.0.0/Chart.min.js"></script>
    <script src="/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="/weatherPortal.js"></script>
</body>
</html>
