<?php
    function queryToDB($query) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "esp32piano";
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            die("Connessione fallita");
        } 
        $result = mysqli_query($conn, $query);
        
        if(!$result) {
            exit("Errore: impossibile eseguire la query" . mysqli_error($conn));
        }

        mysqli_close($conn);
        
        return $result;
    }



    if(isset($_POST["pin"])) {
        $pin_json = file_get_contents('pinValuesStats.json');
        $decoded_json = json_decode($pin_json, true);
        if (isset($decoded_json[$_POST["pin"]])) {
            $decoded_json[$_POST["pin"]]++;
            $f = fopen("pinValuesStats.json", "w");
            fwrite($f, json_encode($decoded_json));
            fclose($f);
            $query = "INSERT INTO `letture`(`ID`, `PinID`, `time`, `datajson`) VALUES (NULL, ".$_POST["pin"].", current_timestamp(), '".json_encode($decoded_json)."')";
            queryToDB($query);
             
         } else {
             echo 'Errore, pin non valido';
         }
    }

    if(isset($_GET["pin"])) {
        $pin_json = file_get_contents('pinValuesStats.json');
        $decoded_json = json_decode($pin_json, true);
        if (isset($decoded_json[$_GET["pin"]])) {
            echo $decoded_json[$_GET["pin"]];
        } else {
            echo 'Errore, pin non valido';
        }
    }

    if(isset($_GET["type"])) {

        if ($_GET["type"] == "get_latest_json") {
            //continua da qua
            $query = "SELECT `datajson` FROM `letture` WHERE ";
        }
        if ($_GET["type"] == "fetch_notes") {
            $query = "SELECT `PinID`, `time` FROM `letture`";
            $result = queryToDB($query);
            echo '
            <table border=2>
                <tr> 
                    <th>PinID</th>
                    <th>time</th>
                </tr>
            ';
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' .$row['PinID']. '</td>';
                echo '<td>' .$row['time']. '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
?>