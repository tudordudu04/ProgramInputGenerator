<?php
    include '../database/db_connection.php';

    session_start();
    header('Content-Type: application/json');

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    if (!$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    } 

    $checkUserQuery = "SELECT * FROM users WHERE username = $1";
    $checkUserResult = pg_query_params($conn, $checkUserQuery, array($username));
    if (pg_num_rows($checkUserResult) == 1) {
        $row = pg_fetch_assoc($checkUserResult);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['userId'] = $row['id'];
            echo json_encode(['success' => true, 'message' => 'Signed in successfully.']);
            pg_free_result($checkUserResult);
            pg_close($conn);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . pg_last_error($conn)]);
        }
    }

    pg_free_result($checkUserResult);
    pg_close($conn);
} else if(isset($_POST['submit'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

?>