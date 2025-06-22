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

    $friendId = $_POST['id'];
    $action = $_POST['action'];

    if ($action === "accept") {
        $sqlAdd = "INSERT INTO friends (id1, id2) VALUES ($1, $2)";
        $resultAdd = pg_query_params($conn, $sqlAdd, array($userId, $friendId));
        $sqlRemoveReq = "DELETE FROM friend_requests WHERE id1 = $1 AND id2 = $2";
        $resultRemove = pg_query_params($conn, $sqlRemoveReq, array($friendId, $userId));
        //se poate intampla unul din queriuri sa mearga si celalalt nu, trebuie rollback
        if ($resultAdd && $resultRemove) {
            echo json_encode([
                "success" => true,
                "message" => "Friend request accepted."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Database error: ".pg_last_error($conn)
            ]);
        }
    } else if ($action === "deny") {
        $sqlRemoveReq = "DELETE FROM friend_requests WHERE id1 = $1 AND id2 = $2";
        $result = pg_query_params($conn, $sqlRemoveReq, array($friendId, $userId));
        if ($result && pg_affected_rows($result) > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Friend request denied."
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Friend request not found."
            ]);
        }
    } else if($action === "remove") {
        $sqlQuery = "DELETE FROM friends f WHERE f.id1 = $1 AND f.id2 = $2";
        $result1 = pg_query_params($conn, $sqlQuery, array($userId, $_POST['id']));

        $sqlQuery = "DELETE FROM friends f WHERE f.id1 = $2 AND f.id2 = $1";
        $result2 = pg_query_params($conn, $sqlQuery, array($userId, $_POST['id']));

        $affected1 = $result1 ? pg_affected_rows($result1) : 0;
        $affected2 = $result2 ? pg_affected_rows($result2) : 0;

        if($affected1 > 0 || $affected2 > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Friend removed successfully."
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Friend not found"
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