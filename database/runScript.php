<?php
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo "Method not allowed";
        exit;
    }

    include '../database/db_connection.php';

    $cmd = 'sudo -u postgres psql -U postgres -d WebProject -f database-script.sql 2>&1';
    $output = shell_exec($cmd);
    if (strpos($output, 'ERROR:') !== false || strpos($output, 'FATAL:') !== false) {
        echo "<div style='color: red; font-weight: bold;'>Script execution failed!</div><br>";
    } else {
        echo "<div style='color: green; font-weight: bold;'>Script executed successfully!</div><br>";
    }
    echo $output;

?>