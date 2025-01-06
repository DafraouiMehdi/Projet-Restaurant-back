<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");  // Allow any origin (adjust for production)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  // Allowed HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");  // Allowed headers

// Handle pre-flight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);  // Early exit for OPTIONS requests
}

include "../connection.php";  // Include your database connection

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data (JSON body)
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["orderID"])) {
        try {
            // Prepare SQL statement for deletion
            $sql = "DELETE FROM assign_delivery WHERE id = :livID";
            $stmt = $connect->prepare($sql);

            // Bind parameter and execute query
            $stmt->bindParam(":livID", $data["orderID"], PDO::PARAM_INT);
            $del = $stmt->execute();

            if ($del) {
                // If deletion is successful, send success response
                echo json_encode(["status" => "success", "message" => "Order successfully deleted."]);
            } else {
                // If no rows are deleted, send a failure message
                echo json_encode(["status" => "error", "message" => "Failed to delete order."]);
            }
        } catch (Exception $e) {
            // Catch exceptions and respond with an error message
            echo json_encode(["status" => "error", "message" => "An error occurred: " . $e->getMessage()]);
        }
    } else {
        // If no orderID is provided, send an error response
        echo json_encode(["status" => "error", "message" => "Order ID not provided."]);
    }
}
?>
