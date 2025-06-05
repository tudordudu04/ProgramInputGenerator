<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;
    $key = "cevacevacevacevacevacevacevacevacevacevacevacevacevacevacevaceva";
    header('Content-Type: application/json');

    if (!isset($_COOKIE['jwt'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $jwt = $_COOKIE['jwt'];

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userId = $decoded->user_id;

        $checkUserQuery = "SELECT * FROM users WHERE id = $1";
        $checkUserResult = pg_query_params($conn, $checkUserQuery, array($userId));

        $checkProfileQuery = "SELECT * FROM profiles WHERE \"ownerId\" = $1";
        $checkProfileResult = pg_query_params($conn, $checkProfileQuery, array($userId));

        if($checkUserResult === false || $checkProfileResult === false){
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: '.pg_last_error($conn)]);
            exit;
        }

        $rowUser = pg_fetch_assoc($checkUserResult);
        $rowProfile = pg_fetch_assoc($checkProfileResult);
        $profile = [
            'username'   => $rowUser['username'],
            'email'      => $rowUser['email'],
            'firstName'  => $rowProfile['firstName'],
            'lastName'   => $rowProfile['lastName'],
            'phoneNumber'=> $rowProfile['phoneNumber'],
            'address'    => $rowProfile['address'],
            'country'    => $rowProfile['country'],
            'city'       => $rowProfile['city']
        ];
        echo json_encode($profile);
        
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
    }

?>