<?php
$c = curl_init('http://localhost:80/esp32piano/server.php?type=fetch_notes');
$c2 = curl_init('http://localhost:80/esp32piano/server.php?type=get_latest_json');
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
$notes_table = curl_exec($c);

if (curl_error($c))
    die(curl_error($c));

curl_exec($c2);
if (curl_error($c2))
    die(curl_error($c2));
//$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
curl_close($c);
curl_close($c2);
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
                console.log(dps[$i]);
                dataPoints.push({
                    label: [$i],
                    y: dps[$i],
                });
            }
            columnChart.render();
            pieChart.render();
        }
        $.getJSON('json/pinValuesStats.json', addData);
    }
</script>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
        <title>Nicoke Webpage</title>
    </head>

    <body class="has-background-danger">
        <div class="container p-6">
            <div class="box">    
                <div class="has-text-centered">
                    <img src="https://fontmeme.com/permalink/220507/130e24c61af4b747662ecf390743f51e.png" />
                    <div class="p-4">
                        <form id="registerform" action="http://localhost:5050" method="POST">
                            <label for="library">Scegli la libreria di suoni che vuoi utilizzare:</label>
                            <input type="text" name="library">
                            <input type="submit">
                        </form>
                    </div>
                </div>
                <div class="is-flex is-flex-direction-row is-justify-content-space-between is-align-items-top" >
                    <div class="is-flex is-flex-direction-column">
                        <div id="columnChartContainer" style="height: 250px"></div>    
                        <div id="pieChartContainer" style="height: 250px"></div>
                    </div>
                    <div>
                        <h3 class="is-size-3">Ultimi 10 tocchi:</h3>
                        <?php
                        echo $notes_table;
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </body>

</html>