<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;

    $key = "cevacevacevacevacevacevacevacevacevacevacevacevacevacevacevaceva";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }

    if (!isset($_COOKIE['jwt'])) {
        echo json_encode([
            "success" => false,
            "message" => "User not authenticated."
        ]);
        exit;
    }

    $key = "cevacevacevacevacevacevacevacevacevacevacevacevacevacevacevaceva";
    $jwt = $_COOKIE['jwt'];

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userId = $decoded->user_id ?? null;
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid authentication token."
        ]);
        exit;
    }

    if (!$userId) {
        echo json_encode([
            "success" => false,
            "message" => "User ID not found in token."
        ]);
        exit;
    }

    // Retrieve and sanitize user input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');

    // Update the user profile in the database
    try {
        $sqlQuery = "UPDATE profiles
             SET \"firstName\" = $1,
                 \"lastName\" = $2,
                 \"phoneNumber\" = $3,
                 \"address\" = $4,
                 \"country\" = $5,
                 \"city\" = $6
             WHERE \"ownerId\" = $7";
        $result = pg_query_params($conn, $sqlQuery, array($firstName, $lastName, $phoneNumber, $address, $country, $city, $userId));
        $sqlQuery = "UPDATE users
             SET \"username\" = $1,
                 \"email\" = $2
             WHERE \"id\" = $3";
        $secondResult = pg_query_params($conn, $sqlQuery, array($username, $email, $userId));
        if ($result && $secondResult) {
            echo json_encode([
                "success" => true,
                "message" => "Profile updated successfully."
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Database error: " . pg_last_error($conn)
            ]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
?>