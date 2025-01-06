<?php
include "../connection.php";

// Set headers for CORS and JSON response
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1); // Show errors on the screen for debugging
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/error_log.txt');

ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error_log.txt');  // Set path to error log file


// Decode incoming JSON payload
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

// Extract data from the request
$client_no = $data['clientNo'] ?? null;
$livreur_no = $data['deliveryPerson'] ?? null;
$orders = $data['orders'] ?? [];

// Validate input
if (!$client_no || !$livreur_no || empty($orders)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Insert into the database
    $stmt = $connect->prepare("
        INSERT INTO assign_delivery (client_no, livreur_no, order_details)
        VALUES (:client_no, :livreur_no, :order_details)
    ");
    
    $del = $connect->prepare("
        DELETE FROM commandes WHERE client_no = :client_no;
    ");

    $stmt->execute([
        ':client_no' => $client_no,
        ':livreur_no' => $livreur_no,
        ':order_details' => json_encode($orders)
    ]);

    $del->execute([
        ':client_no' => $client_no
    ]);

    echo json_encode(['success' => true, 'message' => 'Delivery assigned successfully']);
} catch (PDOException $e) {
    // Log detailed error and return a user-friendly message
    error_log("PDOException: " . $e->getMessage());  // Log detailed error
    echo json_encode(['success' => false, 'message' => 'Failed to assign delivery', 'error' => 'Database error']);
}
?>
