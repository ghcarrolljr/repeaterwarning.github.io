<?php

function makeDrawChart($strLabel, $strDivId, $objOptions, $valuetoDisplay, $maxFlux) {
        echo "// --- $strDivId ------------------------------\r\n";
        echo "var data$strDivId = google.visualization.arrayToDataTable([\r\n";
        echo "  ['Label', 'Value'],['$strLabel', 0]\r\n";
        echo "]);\r\n";
        echo "var chart$strDivId = new google.visualization.Gauge(document.getElementById('$strDivId'));\r\n";
        echo "chart$strDivId.draw(data$strDivId, $objOptions);\r\n";
        echo "function update$strDivId() {\r\n";
        echo "  data$strDivId.setValue(0, 1, fluctuate($valuetoDisplay, $maxFlux));\r\n";
        echo "  chart$strDivId.draw(data$strDivId, $objOptions);\r\n";
        echo "  setTimeout(function() { update$strDivId(); }, Math.round(Math.random()*10000));\r\n";
        echo "}\r\n";
        echo "update$strDivId();";
        echo "\r\n\r\n";
}

function writeCommonOptions() {
        echo "width: 200, height: 200, minorTicks: 1,\r\n";
        echo "min: 10.0, max: 14.0\r\n";
}

?><html>
        <head>
                <title>Repeater Status Dashboard</title>
                <style type="text/css">
                        .repeater {
                                width: 33.3%;
                                float: left;
                                background-color: gray;
                        }
                        .repeater .title {
                                font-size: 1.5em;
                                font-weight: bold;
                                background-color: green;
                        }
                </style>
        </head>
        <body>
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <link rel="preconnect" href="https://fonts.gstatic.com">
                <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">
                <div class="repeater">
                        <div class="title">W5AUU-1 - 146.97 - Conway / Clearwell</div>
                        <div id="chart1a" class="chart"></div>
                        <div id="chart1b" class="chart"></div>
                        <div id="chart1c" class="chart"></div>
                </div>
                <div class="repeater">
                        <div class="title">W5AUU-2 - 147.03 - Conway / OEM</div>
                        <div id="chart2a" class="chart"></div>
                        <div id="chart2b" class="chart"></div>
                        <div id="chart2c" class="chart"></div>
                </div>
                <div class="repeater">
                        <div class="title">W5AUU-3 - 146.625 - Greenbrier</div>
                        <div id="chart3a" class="chart"></div>
                        <div id="chart3b" class="chart"></div>
                        <div id="chart3c" class="chart"></div>
                </div>
                <script type="text/javascript">
                        var jsonData; // Declared here to it is accessible globally

                        function fluctuate(val, maxFluctuation){
                                if (maxFluctuation == 0) {
                                        return val;
                                }
                                else {
                                        var r = Math.random();
                                        var flux;

                                        if (maxFluctuation < 1) {
                                                flux = maxFluctuation;
                                        }
                                        else {
                                                flux = Math.floor(r * (maxFluctuation+1));
                                        }

                                        if ((r*100) % 2 == 0) {
                                                rtnValue = val - flux;
                                        }
                                        else {
                                                rtnValue = val + flux;
                                        }
                                        return rtnValue;
                                }
                        }

                        $.getJSON("/RepeaterWarning.net/json.php", function(result){
                                jsonData = result;
                        });

                        setInterval(function() {
                                $.getJSON("/RepeaterWarning.net/json.php", function(result){
                                        jsonData = result;
                                });
                        }, 60000);

                        google.charts.load('current', {'packages':['gauge']});
                        google.charts.setOnLoadCallback(drawCharts);

                        var options = {
                                battery : {
                                        <?php writeCommonOptions() ?>,
                                        redFrom: 10, redTo: 11,
                                        yellowFrom:11, yellowTo: 12,
                                        greenFrom: 12, greenTo: 14,
                                        min: 10.0, max: 14.0
                                },
                                time : {
                                        <?php writeCommonOptions() ?>,
                                        redFrom: 1000, redTo: 2000,
                                        yellowFrom:900, yellowTo: 1000,
                                        greenFrom: 0, greenTo: 900,
                                        min: 0, max: 2000
                                },
                                power : {
                                        <?php writeCommonOptions() ?>,
                                        redFrom: 0, redTo: 750,
                                        greenFrom: 750, greenTo: 1500,
                                        min: 0, max: 1500
                                }
                        };

                        function drawCharts() {
                                if (jsonData) { // Don't run this unless jsonData is defined (might still be loading)
<?php

makeDrawChart("Battery (v)", "chart1a", "options.battery", "jsonData.repeaters.entries['W5AUU-1'].voltage/10",0);

$date = new DateTime();
$currentTimestamp = $date->getTimestamp();
$reportTime = "jsonData.repeaters.entries['W5AUU-1'].lastStatusTime";
$diffTime = "$currentTimestamp-$reportTime";
//$diffTime = $currentTime -$reportTime;
makeDrawChart("Time (min)", "chart1b", "options.time", $diffTime, 0);
makeDrawChart("Grid power", "chart1c", "options.power", "jsonData.repeaters.telemetry['W5AUU-1'].telemetry5",5);

$date = new DateTime();
$currentTimestamp = $date->getTimestamp();
$reportTime = "jsonData.repeaters.entries['W5AUU-2'].lastStatusTime";
$diffTime = "$currentTimestamp-$reportTime";makeDrawChart("Battery (v)", "chart2a", "options.battery", "jsonData.repeaters.entries['W5AUU-2'].voltage/10",0);
makeDrawChart("Time (min)", "chart2b", "options.time", $diffTime,0);
makeDrawChart("Grid power", "chart2c", "options.power", "jsonData.repeaters.telemetry['W5AUU-2'].telemetry5",5);

$date = new DateTime();
$currentTimestamp = $date->getTimestamp();
$reportTime = "jsonData.repeaters.entries['W5AUU-3'].lastStatusTime";
$diffTime = "$currentTimestamp-$reportTime";
makeDrawChart("Battery (v)", "chart3a", "options.battery", "jsonData.repeaters.entries['W5AUU-3'].voltage/10",0);
makeDrawChart("Time (min)", "chart3b", "options.time", $diffTime,0);
makeDrawChart("Grid power", "chart3c", "options.power", "jsonData.repeaters.telemetry['W5AUU-3'].telemetry3*50",5);

?>

                                }
                                else {
                                        setTimeout(function() { drawCharts(); }, 500); // Try again
                                }
                        }
                </script>
        </body>
</html>
