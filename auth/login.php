<?php
    require_once "../util/vendor/autoload.php";

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;

    $key = "cevacevacevacevacevacevacevacevacevacevacevacevacevacevacevaceva";

    include '../database/db_connection.php';
    header('Content-Type: application/json');

    if(isset($_COOKIE['jwt'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Already logged in.']);

    } else if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $password = htmlspecialchars(trim($_POST['password'] ?? ''));

        if (!$username || !$password) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Please fill in all fields.']);
            exit;
        } 

        $checkUserQuery = "SELECT * FROM users WHERE username = $1";
        $checkUserResult = pg_query_params($conn, $checkUserQuery, array($username));
        
        if($checkUserResult === false){
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: '.pg_last_error($conn)]);
            exit;

        } else if (pg_num_rows($checkUserResult) == 1) {
            $row = pg_fetch_assoc($checkUserResult);
            
            if (password_verify($password, $row['password'])) {
                $iss_time = time();
                $payload = [
                    "iss" => "http://www.ProgramInputGenerator.com",
                    "iat" => $iss_time,
                    "exp" => $iss_time + 3600,
                    "user_id" => $row["id"]
                ];
                $jwt = JWT::encode($payload, $key, 'HS256');
                
                setcookie("jwt", $jwt, [
                    "expires" => time() + 3600,
                    "httponly" => true,
                    "samesite" => "Lax",
                    "path" => "/"
                ]);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Signed in successfully.']);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid username or password.']);
            }
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid username or password.']);
    }
    pg_free_result($checkUserResult);

    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid request.']);
    }
    pg_close($conn);
?>