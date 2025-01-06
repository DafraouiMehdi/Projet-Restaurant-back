<?php

// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//     http_response_code(200);
//     exit();
// }


// header("Content-Type: application/json");

// include "../connection.php";

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $data = json_decode(file_get_contents("php://input"));
    
//     if ($data) {
//         // Préparez les données pour l'update
//         $no = $data->no;
//         $first_name = !empty($data->first_name) ? $data->first_name : null;
//         $last_name = !empty($data->last_name) ? $data->last_name : null;
//         $email = !empty($data->email) ? $data->email : null;
//         $adresse = !empty($data->adresse) ? $data->adresse : null;
//         $password = !empty($data->password) ? $data->password : null;
//         $phone = !empty($data->phone) ? $data->phone : null;

//         // Préparer la requête SQL
//         $sql = "UPDATE clients SET 
//                 first_name = COALESCE(?, first_name), 
//                 last_name = COALESCE(?, last_name), 
//                 email = COALESCE(?, email), 
//                 adresse = COALESCE(?, adresse), 
//                 password = COALESCE(?, password), 
//                 phone = COALESCE(?, phone) 
//                 WHERE no = ?";

//         try {
//             // Préparez la requête
//             $stmt = $connect->prepare($sql);
//             $stmt->execute([$first_name, $last_name, $email, $adresse, $password, $phone, $no]);
            
//             // Vérifiez si la requête a affecté des lignes
//             if ($stmt->rowCount() > 0) {
//                 $response = [
//                     'success' => true,
//                     'message' => 'Informations mises à jour avec succès.',
//                     'user' => [
//                         'no' => $no,
//                         'firstname' => $first_name ?: $data->first_name,
//                         'lastname' => $last_name ?: $data->last_name,
//                         'email' => $email ?: $data->email,
//                         'adresse' => $adresse ?: $data->adresse,
//                         'password' => $password ?: $data->password,
//                         'phone' => $phone ?: $data->phone,
//                     ]
//                 ];
//                 echo json_encode($response);
//                 exit;
//             } else {
//                 $response = [
//                     'success' => false,
//                     'message' => 'Aucune modification détectée ou une erreur est survenue.'
//                 ];
//             }
//         } catch (PDOException $e) {
//             $response = [
//                 'success' => false,
//                 'message' => 'Erreur de mise à jour : ' . $e->getMessage()
//             ];
//         }
//     } else {
//         $response = [
//             'success' => false,
//             'message' => 'Données manquantes ou invalides.'
//         ];
//     }

//     echo json_encode($response);
// } 


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

include "../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if ($data) {
        // Prepare data for update
        $no = $data->no;
        $first_name = !empty($data->first_name) ? $data->first_name : null;
        $last_name = !empty($data->last_name) ? $data->last_name : null;
        $email = !empty($data->email) ? $data->email : null;
        $adresse = !empty($data->adresse) ? $data->adresse : null;
        $password = !empty($data->password) ? $data->password : null; // Secure password
        $phone = !empty($data->phone) ? $data->phone : null;

        // Prepare SQL query
        $sql = "UPDATE clients SET 
                first_name = COALESCE(?, first_name), 
                last_name = COALESCE(?, last_name), 
                email = COALESCE(?, email), 
                adresse = COALESCE(?, adresse), 
                password = COALESCE(?, password), 
                phone = COALESCE(?, phone) 
                WHERE no = ?";

        try {
            // Prepare query
            $stmt = $connect->prepare($sql);
            $stmt->execute([$first_name, $last_name, $email, $adresse, $password, $phone, $no]);
            
            // Check if query affected any rows
            if ($stmt->rowCount() > 0) {
                $response = [
                    'success' => true,
                    'message' => 'Informations mises à jour avec succès.',
                    'user' => [
                        'no' => $no,
                        'first_name' => $first_name ?: $data->first_name,
                        'last_name' => $last_name ?: $data->last_name,
                        'email' => $email ?: $data->email,
                        'adresse' => $adresse ?: $data->adresse,
                        'password' => $password ?: $data->password,
                        'phone' => $phone ?: $data->phone,
                    ]
                ];
                echo json_encode($response);
                exit;
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Aucune modification détectée ou une erreur est survenue.'
                ];
            }
        } catch (PDOException $e) {
            $response = [
                'success' => false,
                'message' => 'Erreur de mise à jour : ' . $e->getMessage()
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Données manquantes ou invalides.'
        ];
    }

    echo json_encode($response);
}
?>
