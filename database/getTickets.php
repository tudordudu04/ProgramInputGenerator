<?php
    include 'decodeUserId.php';
    header('Content-Type: application/json');

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }

    $isAdmin = false;

    if($_POST['forWhom'] !== "myself"){
        $sqlQuery = "SELECT t.id AS \"ticketId\", t.title, t.type, t.body, t.status,
        s.id AS \"userId\", s.username FROM tickets t JOIN users s ON s.id = t.\"ownerId\"";
        $isAdmin = true;
    }
    else 
        $sqlQuery = "SELECT t.id AS \"ticketId\", t.title, t.type, t.body, t.status,
        s.id AS \"userId\", s.username FROM tickets t JOIN users s ON s.id = t.\"ownerId\" WHERE t.\"ownerId\" = $userId";
    $result = pg_query($conn, $sqlQuery);

    if(!$result){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Tickets not found"
        ]);
        exit;
    }
    
    $tickets = [];
    if($isAdmin)
        while($row = pg_fetch_assoc($result)){
            $tickets[] = [
                'id' => $row['ticketId'],
                'title' => $row['title'],
                'type' => $row['type'],
                'body' => $row['body'],
                'status' => $row['status'],
                'isAdmin' => true, 
                'username' => $row['username']
            ];
        }
    else
        while($row = pg_fetch_assoc($result)){
            $tickets[] = [
                'id' => $row['ticketId'],
                'title' => $row['title'],
                'type' => $row['type'],
                'body' => $row['body'],
                'status' => $row['status'],
            ];
        }
    echo json_encode($tickets);
?>