<?php
    include "decodeUserId.php";
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }

    if (!isset($_FILES['profilePhoto'])) {
        echo json_encode([
            "success" => false,
            "message" => "No file uploaded."
        ]);
        exit;
    }

    $file = $_FILES['profilePhoto'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'profile_' . $userId . '_' . time() . '.' . $ext;
    $uploadPath = '../profilePhotos/' . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {

        $sqlQuery = "UPDATE profiles SET \"profilePhotoUrl\" = $1 WHERE \"ownerId\" = $2";
        $result = pg_query_params($conn, $sqlQuery, array('../profilePhotos/' . $newFileName, $userId));

        if ($result) {
            echo json_encode([
                "success" => true,
                "message" => "Profile photo updated successfully.",
                "url" => "../profilePhotos/" . $newFileName
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Database error: " . pg_last_error($conn)
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to move uploaded file."
        ]);
    }
?>