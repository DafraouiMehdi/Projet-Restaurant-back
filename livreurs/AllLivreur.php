<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Prevent any unwanted output
ob_clean();

// CORS headers and database logic
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle OPTIONS request (Preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
// Include the database connection
include "../connection.php";

try {
    // Prepare the SQL query to join 'commandes' and 'plats' on 'plat_no'
    $sql = "SELECT * FROM livreurs";

    $stmt = $connect->prepare($sql);

    // Execute the query
    $stmt->execute();

    // Fetch all orders from the database
    $liv = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Close the connection (optional)
    $connect = null;

    // Send the data as JSON
    echo json_encode($liv);

} catch (PDOException $e) {
    // Handle error and return an empty array
    echo json_encode([]);
    echo "Error: " . $e->getMessage();
}
?>
