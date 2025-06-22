<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';
    header('Content-Type: application/json');

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;

    $isAdmin = false;

    if (isset($_COOKIE['jwt'])) {
        $key = "cevacevacevacevacevacevacevacevacevacevacevacevacevacevacevaceva";
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