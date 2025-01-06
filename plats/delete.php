<?php
header('Content-Type: application/json'); // Ensure the response is JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check for OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../connection.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the 'no' parameter from the POST request
    $platNo = $_POST['no'];

    if (empty($platNo)) {
        // Return an error if 'no' is not provided
        echo json_encode(["success" => false, "message" => "Plat ID is required."]);
        exit;
    }

    try {
        // Prepare and execute the delete query
        $stmt = $connect->prepare("DELETE FROM plats WHERE no = :no");
        $stmt->execute(["no" => $platNo]);

        // Check if any rows were affected (deleted)
        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Item deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No item found with the given ID."]);
        }
    } catch (PDOException $e) {
        // Handle any database errors
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>
