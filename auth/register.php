<?php
include '../database/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    if (!$username || !$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    }

    $checkEmailQuery = "SELECT email FROM users WHERE email = $1";
    $checkEmailResult = pg_query_params($conn, $checkEmailQuery, array($email));
    $checkUsernameQuery = "SELECT username FROM users WHERE username = $1";
    $checkUsernameResult = pg_query_params($conn, $checkUsernameQuery, array($username));

    if (pg_num_rows($checkUsernameResult) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        exit;

    } else if (pg_num_rows($checkEmailResult) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit;

    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo json_encode(['success' => false, 'message' => 'Email isn\'t valid.']);
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        

        //aici ar trebuii sa folosesc un trigger ca sa fac urmatorul id la user
        $insertQuery = "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)";
        $result = pg_query_params($conn, $insertQuery, array($username, $email, $hashedPassword));

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully.']);
            pg_free_result($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . pg_last_error($conn)]);
        }
    }
    pg_free_result($checkEmailResult);
    pg_close($conn);
    
} else if(isset($_POST['submit'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

?>