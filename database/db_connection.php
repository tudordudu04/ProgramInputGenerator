<?php
    require '../util/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->load();

    $dbHost = $_ENV['DB_HOST'];
    $dbUser = $_ENV['DB_USER'];
    $dbPass = $_ENV['DB_PASS'];
    $dbName = $_ENV['DB_NAME'];
    
    $connection_string = "host=$dbHost dbname=$dbName user=$dbUser password=$dbPass";
    $conn = pg_connect($connection_string);
?>