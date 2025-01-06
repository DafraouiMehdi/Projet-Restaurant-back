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
    // Get the 'orderId' from the POST request body
    $data = json_decode(file_get_contents("php://input"), true);
    $orderId = $data['orderId'];  // Use 'orderId' instead of 'orderID'

    // Check if orderId is provided
    if (empty($orderId)) {
        // Return an error if 'orderId' is not provided
        echo json_encode(["status" => "error", "message" => "Order ID is required."]);
        exit;
    }

    try {
        // Prepare and execute the delete query
        $stmt = $connect->prepare("DELETE FROM assign_delivery WHERE id = :orderId");
        $stmt->execute(["orderId" => $orderId]);

        // Check if any rows were affected (deleted)
        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Order deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "No order found with the given ID."]);
        }
    } catch (PDOException $e) {
        // Handle any database errors
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>
