<?php

header("Access-Control-Allow-Origin: *"); // Autorise toutes les origines. Remplacez '*' par une origine spécifique si nécessaire.
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Méthodes autorisées
header("Access-Control-Allow-Headers: Content-Type, Authorization");


include "../connection.php"; // Connexion à la base de données

try {
    $sql = "SELECT * FROM plats"; // Requête pour récupérer tous les plats
    
    $stmt = $connect->prepare($sql);
    $stmt->execute(); // Exécution de la requête
    
    if ($stmt->rowCount() > 0) {
        $plats = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats sous forme de tableau associatif
        echo json_encode($plats); // Retourner les plats au format JSON
    } else {
        echo json_encode([]); // Retourne un tableau vide si aucun plat trouvé
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Connection Error: " . $e->getMessage()]); // Gestion des erreurs
    exit;
}
?>
