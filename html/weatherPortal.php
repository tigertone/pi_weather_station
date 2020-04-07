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
        <button class="tablinks" onclick="selectTab(event, 'Today')" id="defaultOpen">Today
        </button>
        <button class="tablinks" onclick="selectTab(event, 'Annual')">Annual
        </button>
    </div>
</head>

<body>
     <div class="chartContainer" id="chartContainerID">

</div>

<div id="legend">
    <span id="legendLine1" class="legendLines"></span><span id="legendLabel1" class="legendText"></span><span id="legendLine2" class="legendLines"></span><span id="legendLabel2" class="legendText"></span><br/>
</div>
    <div id="footer">
		
        Internal status: <span id="statusInternal" class="statusText"></span><br/>
        External status: <span id="statusExternal" class="statusText"></span><br/>
	Battery: <span id="statusVoltageExternal" class="statusText"></span><br/>
        External success rate: <span id="statusPercentExternal" class="statusText"></span><br/>
    </div>

    <script src="/chartJs_2.7.2/Chart.bundle.min.js">
    </script>
    <script src="/weatherPortal.js">
    </script>
</body>
</html>
