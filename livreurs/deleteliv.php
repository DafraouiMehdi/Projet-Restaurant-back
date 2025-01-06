<?php
// Enable error reporting for debugging (disable this in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set CORS headers (if needed)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Include database connection
include "../connection.php";

// Handle pre-flight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data (JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if all required fields are provided
    if (isset($data['first_name'], $data['last_name'], $data['email'])) {
        try {
            
            $dele = $connect->prepare("DELETE FROM inscr_livreur WHERE first_name = ? AND last_name = ? AND email = ?");

            $dele->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
            ]);

            echo json_encode(['success' => true, 'message' => 'Livreur Rejeter.']);

        } catch (Exception $e) {
            // Log the error (do not display detailed error messages in production)
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion ou de l\'envoi de l\'email.']);
        }
    } else {
        // Missing required data
        echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    }
} else {
    // Method not allowed
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>
