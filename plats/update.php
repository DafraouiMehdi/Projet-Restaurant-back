<?php

// Handle CORS
header("Access-Control-Allow-Origin: *"); // Or specify your frontend URL for security
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle the preflight request
    header("HTTP/1.1 200 OK");
    exit;
}

include "../connection.php";

header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Check if POST parameters are set
        if (!isset($_POST["no"], $_POST["name"], $_POST["price"], $_POST["description"])) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "error", "error" => "Les paramètres requis sont manquants."]);
            exit;
        }

        // Sanitize and trim inputs
        $plat_no = trim($_POST["no"]);
        $plat_name = trim($_POST["name"]);
        $plat_price = trim($_POST['price']);
        $plat_description = trim($_POST['description']);

        // Validation des champs vides
        if (empty($plat_no) || empty($plat_name) || empty($plat_price) || empty($plat_description)) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "error", "error" => "L'un des champs est vide."]);
            exit;
        }

        // Validation du prix
        if (!is_numeric($plat_price) || $plat_price <= 0) {
            http_response_code(400);
            echo json_encode(["message" => "error", "error" => "Le prix doit être un nombre valide et supérieur à zéro."]);
            exit;
        }

        // Prepare SQL query for updating the dish
        $sql = "UPDATE plats SET name = :newname, price = :newprice, description = :newdesc WHERE no = :platno";
        $stmt = $connect->prepare($sql);

        // Execute the statement with the sanitized inputs
        $res = $stmt->execute([
            "newname" => $plat_name,
            "newprice" => $plat_price,
            "newdesc" => $plat_description,
            "platno" => $plat_no
        ]);

        if ($res) {
            http_response_code(200); // OK
            echo json_encode(["message" => "success", "message_details" => "Le plat a été mis à jour avec succès."]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "error", "error" => "Échec de la mise à jour du plat."]);
        }
    } else {
        // If the request method is not POST
        http_response_code(405); // Method Not Allowed
        echo json_encode(["message" => "error", "error" => "Méthode HTTP non autorisée."]);
    }
} catch (Exception $th) {
    http_response_code(500);
    echo json_encode(["message" => "error", "error" => "Erreur de connexion : " . $th->getMessage()]);
    exit;
}
