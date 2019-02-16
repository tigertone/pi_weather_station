<!DOCTYPE html>
<html manifest="weatherPortal.appcache">




<head>

<link rel="manifest" href="/manifest.json">
<link rel="stylesheet" type="text/css" href="weatherPortal.css"><meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="viewport" content="width=device-width, initial-scale=1">


    <div class="tab">
        <button class="tablinks" onclick="selectTab(event, 'Today')" id="defaultOpen">Today
        </button>
        <button class="tablinks" onclick="selectTab(event, 'Annual')">Annual
        </button>
    </div>
</head>

<body>
     <div class="chart-container">

        <div class="lineChartCanvas">
            <canvas id="lineChart1">
            <canvas>
        </div>

        <div class="lineChartCanvas">
            <canvas id="lineChart2">
            </canvas>
        </div>

        <div class="lineChartCanvas">
            <canvas id="lineChart3">
            </canvas>
        </div>

</div>


    <div id="footer">
        Internal status: <span id="statusInternal" class="statusText"></span>    <br/>
        External status: <span id="statusExternal" class="statusText"></span><br/>
        Battery: <span id="statusVoltageExternal" class="statusText"></span><br/>
        External success rate: <span id="statusPercentExternal" class="statusText"></span>
    </div>

    <script src="/chartJs_2.7.2/Chart.bundle.min.js">
    </script>
    <script src="/weatherPortal.js">
    </script>
</body>
</html>
