<?php
    require_once '../util/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable("..");
    $dotenv->load();

    $connection_string = sprintf(
        "host=%s dbname=%s user=%s password=%s",
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );

    $conn = pg_connect($connection_string);
?>