<?php
// // Set headers to allow cross-origin requests and JSON content type

header("Access-Control-Allow-Origin: http://127.0.0.1:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Content-Type: application/json");

// Enable error reporting for debugging (development mode only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Include the database connection
    include "../connection.php";

    // Get the JSON data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate that data is received and is in the correct format
    if (empty($data) || !is_array($data)) {
        throw new Exception("Invalid or missing data.");
    }

    // Prepare the SQL query once for better performance
    $query = "INSERT INTO commandes (client_no, plat_no, date_comm, quantite, price)
              VALUES (:client_no, :plat_no, :date_comm, :quantite, :price)";
    $stmt = $connect->prepare($query);

    $hist = "INSERT INTO historique_commande (client_no, plat_no, date_comm, quantite, price)
              VALUES (:client_no, :plat_no, :date_comm, :quantite, :price)";
    $histres = $connect->prepare($hist);

    // Iterate over each order and validate required fields
    foreach ($data as $order) {
        if (!isset($order['client_id'], $order['plat_id'], $order['quantity'], $order['price'])) {
            throw new Exception("Incomplete order data: client_id, plat_id, quantity, and price are required.");
        }

        // Sanitize and validate input data
        $client_no = (int) $order['client_id'];
        $plat_no = (int) $order['plat_id'];
        $quantite = (int) $order['quantity'];
        $price = (float) $order['price'];
        $date_comm = date('Y-m-d H:i:s');

        if ($client_no <= 0 || $plat_no <= 0 || $quantite <= 0 || $price <= 0) {
            throw new Exception("Invalid values for client_id, plat_id, quantity, or price.");
        }

        // Execute the prepared statement
        $stmt->execute([
            ':client_no' => $client_no,
            ':plat_no' => $plat_no,
            ':date_comm' => $date_comm,
            ':quantite' => $quantite,
            ':price' => $price,
        ]);
    }

    // Return a JSON success response
    echo json_encode([
        "success" => true,
        "message" => "Orders successfully saved!",
    ]);
} catch (PDOException $e) {
    // Handle database-related errors
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage(),
    ]);
    exit;
} catch (Exception $e) {
    // Handle general errors
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage(),
    ]);
    exit;
}
?>
