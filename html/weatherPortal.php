<!DOCTYPE html>
<html manifest="weatherPortal.appcache">
	
<head>
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" type="text/css" href="weatherPortal.css">

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <div class="tab">
        <button class="tablinks" onclick="selectTab(event, 'Today')" id="defaultOpen">Today
        </button>
        <button class="tablinks" onclick="selectTab(event, 'Annual')">Annual
        </button>
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


    <div id="footer">
        Status: <span id="statusIcon" class="statusIcon"></span>
    </div>

    <script src="/chartJs_2.7.2/Chart.bundle.min.js">
    </script>
    <script src="/weatherPortal.js">
    </script>
</body>
</html>
