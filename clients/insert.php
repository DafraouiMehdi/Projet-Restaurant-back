<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; 
}

include "../connection.php";

// Decode incoming JSON request
$data = json_decode(file_get_contents("php://input"), true);

// Validate and handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($data)) {
    try {
        // Retrieve data from request
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $adresse = $data['adresse'] ?? null;
        $phone = $data['phone'] ?? null;

        // Check required fields
        if (!$firstname || !$lastname || !$email || !$password || !$adresse || !$phone) {
            echo json_encode(["message" => "Missing required fields."]);
            http_response_code(400);
            exit;
        }

        // Prepare SQL query to insert client data
        $sql = "INSERT INTO clients (first_name, last_name, email, password, adresse, phone) 
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

        // Fetch the last inserted user to include in the response
        $user_id = $connect->lastInsertId();
        $user_stmt = $connect->prepare("SELECT * FROM clients WHERE no = :id");
        $user_stmt->execute([':id' => $user_id]);
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

        // Respond with success and user data
        echo json_encode([
            'status' => 'success',
            'url' => "http://127.0.0.1:8080",
            'user' => $user
        ]);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        http_response_code(500);
    }
} else {
    // Handle invalid request data
    echo json_encode(["message" => "Invalid data received"]);
    http_response_code(400);
}
?>
