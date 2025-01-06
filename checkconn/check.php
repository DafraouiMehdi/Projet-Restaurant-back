<?php
// header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: http://localhost:4433'); // Restrict origin
// header('Access-Control-Allow-Methods: POST');
// header('Access-Control-Allow-Headers: Content-Type');


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://127.0.0.1:8080'); // Allow requests from the frontend origin
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Allow POST and OPTIONS requests
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Send a 200 OK for preflight
    exit;
}
try {
    $dsn = 'mysql:host=localhost;dbname=pjres;charset=utf8mb4';
    $username = 'root';
    $password = '';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $connect = new PDO($dsn, $username, $password, $options);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data.']);
        exit;
    }

    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
        exit;
    }

    $tables = [
        'admin' => 'http://127.0.0.1:8080/admin.html',
        'clients' => 'http://127.0.0.1:8080/desc.html',
        'livreurs' => 'http://127.0.0.1:8080/livreur.html'
    ];

    foreach ($tables as $table => $url) {
        $sql = "SELECT * FROM $table WHERE email = :email AND password = :pass";
        $stmt = $connect->prepare($sql);
        $stmt->execute(['email' => $email , 'pass' => $password]);
        $user = $stmt->fetch();

        if ($user) {
            // If the user is found and password matches
            if ($table === 'admin') {
                // If it's an admin, automatically send the admin URL
                echo json_encode([
                    'status' => 'success',
                    'url' => $url,
                ]);
                exit;
            } else {
                // If it's a client or livreur, return user data
                $response = [
                    'status' => 'success',
                    'url' => $url,
                    'user' => [
                        'no' => $user['no'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'email' => $user['email'],
                        'adresse' => $user['adresse'],
                        'password' => $user['password'],
                        'phone' => $user['phone']
                    ]
                ];
                echo json_encode($response);
                exit;
            }
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'Incorrect credentials.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection error.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
