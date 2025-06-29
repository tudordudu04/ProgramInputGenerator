<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;

    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->load();

    $isAdmin = false;

    if (isset($_COOKIE['jwt'])) {
        $key = $_ENV['JWT_SECRET'];
        $jwt = $_COOKIE['jwt'];

        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $userId = $decoded->user_id ?? null;
        } catch (Exception $e) {
            exit;
        }
        $sqlQuery = "SELECT \"isAdmin\" FROM profiles WHERE \"ownerId\" = $1";
        $result = pg_query_params($conn, $sqlQuery, array($userId));

        if ($result && $row = pg_fetch_assoc($result)) {
            if($row['isAdmin'] === "t") 
                $isAdmin = true;
        }
    }
?>