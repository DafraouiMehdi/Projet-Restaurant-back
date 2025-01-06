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

// Include database connection
include "../connection.php";

// Retrieve the POST data sent from the frontend
$data = json_decode(file_get_contents("php://input"), true);

// Check if the user number (no) is provided
if (!isset($data['user_no'])) {
    echo json_encode(['status' => 'error', 'message' => 'User number is required']);
    exit;
}

$user_no = $data['user_no']; // User number from frontend

// Query to fetch orders and client's phone number for the given user
$query = "
    SELECT ass.*, cl.phone
    FROM assign_delivery ass
    JOIN clients cl ON cl.no = ass.client_no
    WHERE ass.livreur_no = :user_no
";

$stmt = $connect->prepare($query);

// Check if the query preparation was successful
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed']);
    exit;
}

// Bind the user_no to the query and execute it
$stmt->bindParam(':user_no', $user_no, PDO::PARAM_INT);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to execute query']);
    exit;
}

// Fetch the orders and phone numbers
$orders_and_phones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if any orders were found
if ($orders_and_phones) {
    // Respond with the orders and phone numbers as a JSON object
    echo json_encode([
        'status' => 'success',
        'orders' => $orders_and_phones
    ]);
} else {
    // If no orders found, return an empty array with success status
    echo json_encode([
        'status' => 'success',
        'orders' => []
    ]);
}



// header('Content-Type: application/json'); // Ensure the response is JSON
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// // Check for OPTIONS request (preflight)
// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//     http_response_code(200);
//     exit;
// }

// // Include database connection
// include "../connection.php";

// // Retrieve the POST data sent from the frontend
// $data = json_decode(file_get_contents("php://input"), true);

// // Check if the user number (no) is provided
// if (!isset($data['user_no'])) {
//     echo json_encode(['status' => 'error', 'message' => 'User number is required']);
//     exit;
// }

// $user_no = $data['user_no']; // User number from frontend

// // Query the database to fetch orders for the given user
// $query = "SELECT * FROM assign_delivery WHERE livreur_no = :user_no";
// $stmt = $connect->prepare($query);

// if (!$stmt) {
//     echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed']);
//     exit;
// }

// $stmt->bindParam(':user_no', $user_no, PDO::PARAM_INT);
// $stmt->execute();

// // Fetch all the orders for the user
// $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Check if orders were found
// if ($orders) {
//     // Respond with the orders as a JSON object
//     echo json_encode(['status' => 'success', 'orders' => $orders]);
// } else {
//     // If no orders found, return an empty array with success status
//     echo json_encode(['status' => 'success', 'orders' => []]);
// }



?>
