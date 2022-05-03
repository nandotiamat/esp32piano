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

        echo "Connessione riuscita<br>";

        $result = mysqli_query($conn, $query);
        
        if(!$result) {
            exit("Errore: impossibile eseguire la query" . mysqli_error($conn));
        }
        echo "Query eseguita con successo.<br>";

        mysqli_close($conn);
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
?>