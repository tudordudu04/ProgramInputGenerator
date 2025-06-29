<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';
    use Firebase\JWT\JWT;

    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->load();

    $key = $_ENV['JWT_SECRET'];
    session_start();

    $client_id = $_ENV['CLIENT_ID'];
    $client_secret = $_ENV['CLIENT_SECRET'];
    $redirect_uri = $_ENV['REDIRECT_URI'];

    if (!isset($_GET['code'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No code provided']);
        exit;
    }

    $code = $_GET['code'];

    $token_url = 'https://oauth2.googleapis.com/token';
    $data = [
        'code' => $code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);
    if (!isset($token_data['access_token'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Failed to get access token']);
        exit;
    }

    $access_token = $token_data['access_token'];

    $userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . urlencode($access_token);
    $userinfo_response = file_get_contents($userinfo_url);
    $userinfo = json_decode($userinfo_response, true);

    if (!isset($userinfo['email'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Failed to get user info']);
        exit;
    }

    $email = $userinfo['email'];
    $name = $userinfo['name'] ?? '';
    $google_id = $userinfo['id'];

    $checkUserQuery = "SELECT * FROM users WHERE email = $1";
    $checkUserResult = pg_query_params($conn, $checkUserQuery, array($email));

    if ($checkUserResult && pg_num_rows($checkUserResult) === 1) {
        $row = pg_fetch_assoc($checkUserResult);
        $user_id = $row['id'];
    } else {
        $insertUserQuery = "INSERT INTO users (username, email, password) VALUES ($1, $2, '') RETURNING id";
        $insertUserResult = pg_query_params($conn, $insertUserQuery, array($email, $email,));
        if ($insertUserResult) {
            $user_id = pg_fetch_result($insertUserResult, 0, 'id');
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create user']);
            exit;
        }
    }

    $iss_time = time();
    $payload = [
        "iss" => "https://www.frankenpig.online",
        "iat" => $iss_time,
        "exp" => $iss_time + 3600*24*7,
        "user_id" => $user_id
    ];
    $jwt = JWT::encode($payload, $key, 'HS256');

    setcookie("jwt", $jwt, [
        "expires" => time() + 3600*24*7,
        "httponly" => true,
        "samesite" => "Lax",
        "path" => "/"
    ]);
    
    header("Location: ../index.php");
    exit;
?>