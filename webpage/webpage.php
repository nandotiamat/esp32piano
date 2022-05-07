<?php
$c = curl_init('http://localhost:80/esp32piano/server.php?type=fetch_notes');
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
$notes_table = curl_exec($c);
if (curl_error($c))
    die(curl_error($c));
//$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
curl_close($c);
?>

<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

<script>
    window.onload = function() {

        var dataPoints = [];

        var columnChart = new CanvasJS.Chart("columnChartContainer", {
            animationEnabled: true,
            theme: "light2",
            zoomEnabled: true,
            title: {
                text: "Pin Records - Histogram"
            },
            axisY: {
                title: "Number of Usages",
            },

            data: [{
                type: "column",
                dataPoints: dataPoints
            }]
        });

        var pieChart = new CanvasJS.Chart("pieChartContainer", {
            animationEnabled: true,
            theme: "light2",
            zoomEnabled: true,
            title: {
                text: "Pin Records - Pie Diagram"
            },
            axisY: {
                title: "Number of Usages",
            },

            data: [{
                type: "pie",
                innerRadius: "40%",
                showInLegend: true,
                legendText: "{label}",
                indexLabel: "{label}: #percent%",
                dataPoints: dataPoints
            }]
        });


        function addData(data) {
            var dps = data;
            for ($i in dps) {
                dataPoints.push({
                    label: [$i],
                    y: dps[$i],
                });
            }
            columnChart.render();
            pieChart.render();
        }

        $.getJSON("pinValuesStats.json", addData);
    }
</script>
<html>

<head>
    <title>Nicoke Webpage</title>
</head>

<body>
    <div>
        <form id="registerform" action="http://localhost:5050" method="POST">
            <label for="library">Scegli la libreria di suoni che vuoi utilizzare:</label>
            <input type="text" name="library">
            <input type="submit">
            <div>
                <h3>Ultimi suoni riprodotti:</h3>
                <?php
                echo $notes_table;
                ?>
            </div>
        </form>
    </div>
    <div id="columnChartContainer" style="height: 300px; width: 75%;"></div>
    <div id="pieChartContainer" style="height: 300px; width: 75%;"></div>

</body>

</html>