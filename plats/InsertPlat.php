<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json'); // Ajouter l'en-tête Content-Type pour JSON

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gérer les requêtes preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../connection.php"; // Inclure la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les données du formulaire sont présentes
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $description = $_POST['description'] ?? null;

    // Vérifier si un fichier d'image a été téléchargé
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image_file']['tmp_name'];
        $imageName = $_FILES['image_file']['name'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = uniqid('dish_', true) . '.' . $imageExtension;

        // Définir le répertoire et le chemin cible pour l'image
        $targetDirectory = __DIR__ . '/images/';
        $targetPath = $targetDirectory . $newImageName;

        // Vérifier si le répertoire existe
        if (!is_dir($targetDirectory)) {
            if (!mkdir($targetDirectory, 0775, true)) {
                echo json_encode(["message" => "error", "error" => "Failed to create the images directory."]);
                exit;
            }
        }

        // Déplacer le fichier téléchargé vers le répertoire cible
        if (!move_uploaded_file($imageTmpName, $targetPath)) {
            echo json_encode(["message" => "error", "error" => "Failed to upload the image."]);
            exit;
        }

        // Préparer la requête SQL pour insérer les données dans la base de données
        try {
            $stmt = $connect->prepare("INSERT INTO plats (name, price, description, image_path) VALUES (:name, :price, :description, :image_name)");

            // Lier les paramètres
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':image_name', $newImageName, PDO::PARAM_STR);

            // Exécuter la requête
            if ($stmt->execute()) {
                $dishId = $connect->lastInsertId(); // Obtenir l'ID du plat inséré

                // Retourner les données insérées dans la réponse
                $imageUrl = 'http://localhost:4433/PHP/PJResBack/plats/images/' . $newImageName;

                echo json_encode([
                    "message" => "success",
                    "id" => $dishId,
                    "name" => $name,
                    "price" => $price,
                    "description" => $description,
                    "image_path" => $imageUrl, // URL complète de l'image
                ]);
            } else {
                echo json_encode(["message" => "error", "error" => "Failed to insert record."]);
            }
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo json_encode(["message" => "error", "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["message" => "error", "error" => "No image uploaded or error with upload."]);
        exit;
    }

    // Fermer la connexion
    $connect = null;
}
?>
