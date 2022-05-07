<?php 
    $c = curl_init('http://localhost:80/esp32piano/server.php?type=fetch_notes');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $notes_table = curl_exec($c);
    if (curl_error($c))
        die(curl_error($c));
    //$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
?>
<script>
    $(function() {
        $('#registerform').submit(function(event) {
            event.preventDefault();
            $(this).submit();
    }); 
});
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
    </body>
</html>

