<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// // Allow CORS for cross-origin requests
// header("Access-Control-Allow-Origin: *"); // Allow all origins (or specify your origin, e.g., "http://127.0.0.1:8080")
// header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    
//     // Handle preflight OPTIONS request
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     exit;
// }

// include "../connection.php";

// // Decode incoming JSON request
// $data = json_decode(file_get_contents("php://input"), true);

// if (!$data) {
//     echo json_encode(["status" => "error", "message" => "Invalid JSON payload."]);
//     http_response_code(400);
//     exit;
// }

// // Validate and handle POST request
// if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($data)) {
//     try {
//         // Retrieve data from request
//         $firstname = $data['firstname'] ?? null;
//         $lastname = $data['lastname'] ?? null;
//         $email = $data['email'] ?? null;
//         $password = $data['password'] ?? null;
//         $adresse = $data['adresse'] ?? null;
//         $phone = $data['phone'] ?? null;

//         // Check required fields
//         if (!$firstname || !$lastname || !$email || !$password || !$adresse || !$phone) {
//             echo json_encode(["message" => "Missing required fields."]);
//             http_response_code(400);
//             exit;
//         }

//         // Prepare SQL query to insert client data
//         $sql = "INSERT INTO inscr_livreur (first_name, last_name, email, password, adresse, phone) 
//                 VALUES (:f, :l, :e, :pass, :a, :phone)";
//         $stmt = $connect->prepare($sql);
//         $stmt->execute([
//             ':f' => $firstname,
//             ':l' => $lastname,
//             ':e' => $email,
//             ':pass' => $password,
//             ':a' => $adresse,
//             ':phone' => $phone
//         ]);

//         echo json_encode([
//             'status' => 'success',
//             'url' => "http://127.0.0.1:8080",
//             'user' => $user
//         ]);
//     } catch (PDOException $e) {
//         // Handle database errors
//         echo json_encode(["message" => "Database error: " . $e->getMessage()]);
//         http_response_code(500);
//     }
// } else {
//     // Handle invalid request data
//     echo json_encode(["message" => "Invalid data received"]);
//     http_response_code(400);
// }
// 

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *"); // Allow all origins, or specify your origin (e.g., http://127.0.0.1:8080)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../connection.php"; // Ensure that the connection to the database is correct

// Decode incoming JSON request
$data = json_decode(file_get_contents("php://input"), true);

// If the JSON payload is invalid
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON payload."]);
    http_response_code(400);
    exit;
}

// Validate and handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($data)) {
    try {
        // Retrieve data from the request
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $adresse = $data['adresse'] ?? null;
        $phone = $data['phone'] ?? null;

        // Check if all required fields are provided
        if (!$firstname || !$lastname || !$email || !$password || !$adresse || !$phone) {
            echo json_encode(["status" => "error", "message" => "Missing required fields."]);
            http_response_code(400);
            exit;
        }

        // Prepare SQL query to insert client data into the database
        $sql = "INSERT INTO inscr_livreur (first_name, last_name, email, password, adresse, phone) 
                VALUES (:f, :l, :e, :pass, :a, :phone)";
        $stmt = $connect->prepare($sql);
        $stmt->execute([
            ':f' => $firstname,
            ':l' => $lastname,
            ':e' => $email,
            ':pass' => $password,
            ':a' => $adresse,
            ':phone' => $phone
        ]);

        // Return a success response with the URL to redirect to
        echo json_encode([
            'status' => 'success',
            'url' => "http://127.0.0.1:8080"
        ]);
    } catch (PDOException $e) {
        // Handle database errors and return an error message
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        http_response_code(500);
    }
} else {
    // Handle invalid or empty request data
    echo json_encode(["status" => "error", "message" => "Invalid data received"]);
    http_response_code(400);
}
?>

