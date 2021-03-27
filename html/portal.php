<!DOCTYPE html>
<html manifest="portal.appcache">




<head>

<link rel="manifest" href="/manifest.json">
<link rel="stylesheet" type="text/css" href="portal.css"><meta name="viewport" content="width=device-width, initial-scale=1">
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

	<table class="summaryTable" id="currentTable" style="display: table">
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


    <table class="summaryTable" id="statusTable" style="display: none">

        <tr>
        	<td style="text-align:right">Internal status:</td>
        	<td style="text-align:left"><span id="statusInternal"></span></td>
        </tr>
        <tr>
        	<td style="text-align:right">External status:</td>
        	<td style="text-align:left"><span id="statusExternal"></span> (<span id="statusPercentExternal"></span>)</td>
        </tr>
		<tr>
			<td style="text-align:right">Battery:</td>
			<td style="text-align:left"><span id="statusVoltageExternal"></span></td>
		</tr>
		<tr>
			<td style="text-align:right">Disk space remaining:</td>
			<td style="text-align:left"><span id="statusSpaceMb"></span> (<span id="statusSpacePercent"></span>)</td>
		</tr>

    </table>

    <script src="/chartJs_3.0.0/chart.js"></script>
    <script src="/chartjs-adapter-date-fns/chartjs-adapter-date-fns.bundle.js"></script>
    <script src="/portal.js"></script>
</body>
</html>
