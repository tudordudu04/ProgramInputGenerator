<?php
    include "decodeUserId.php";
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }


    if (!isset($_POST['id'], $_POST['action'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Missing parameters."
        ]);
        exit;
    }

    $ticketId = $_POST['id'];
    $action = $_POST['action'];

    if ($action === "closed" || $action === "resolved") {
        $sqlQuery = "UPDATE tickets SET status = $1 WHERE id = $2";
        $result = pg_query_params($conn, $sqlQuery, array($action, $ticketId));
        if ($result) {
            echo json_encode([
                "success" => true,
                "message" => "Ticket status updated succesfully."
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Database error: ".pg_last_error($conn)
            ]);
        }
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Unknown action."
        ]);
    }
?>