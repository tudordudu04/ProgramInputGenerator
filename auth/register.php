<?php
    include 'database/db_connection.php';
    header('Content-Type: application/json');

    if(isset($_COOKIE['jwt'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Already logged in.']);
        
    } else if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));
        $password = htmlspecialchars(trim($_POST['password'] ?? ''));

        if (!$username || !$email || !$password) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Please fill in all fields.']);
            exit;
        }

        if (strlen($password) < 8) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Password must be at least 8 characters long.']);
            exit;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Password must contain at least one uppercase letter.']);
            exit;
        }
        if (!preg_match('/[a-z]/', $password)) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Password must contain at least one lowercase letter.']);
            exit;
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Password must contain at least one special character.']);
            exit;
        }

        $checkEmailQuery = "SELECT email FROM users WHERE email = $1";
        $checkEmailResult = pg_query_params($conn, $checkEmailQuery, array($email));
        $checkUsernameQuery = "SELECT username FROM users WHERE username = $1";
        $checkUsernameResult = pg_query_params($conn, $checkUsernameQuery, array($username));
        
        if($checkEmailResult === false || $checkUsernameResult === false){
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . pg_last_error($conn)]);
            exit;
        }

        if (pg_num_rows($checkUsernameResult) > 0) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Username already exists.']);
            exit;

        } else if (pg_num_rows($checkEmailResult) > 0) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Email already exists.']);
            exit;

        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email isn\'t valid.']);
                exit;
            }
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $insertQuery = "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)";
            $result = pg_query_params($conn, $insertQuery, array($username, $email, $hashedPassword));


            if ($result) {
                echo json_encode([
                'success' => true, 
                'message' => 'Account created successfully.']);
                pg_free_result($result);
            } else {
                http_response_code(401);
                echo json_encode(['
                success' => false, 
                'message' => 'Database error: ' . pg_last_error($conn)]);
            }
        }
        pg_free_result($checkEmailResult);
        pg_close($conn);
        
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid request.']);
    }

?>