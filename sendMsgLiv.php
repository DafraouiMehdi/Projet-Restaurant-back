<?php
// This PHP script acts as a proxy to forward the SMS request to Infobip.

$apiUrl = 'https://portal.infobip.com/dev/api-keys/DD2AFF194C00A4AE62E538F1CBA326B6';
$apiKey = 'App dd2aff194c00a4ae62e538f1cba326b6-9213a231-9c6d-4e7f-9aab-34d76e9ae549'; // Replace with your Infobip API key

// Prepare data to send in the request body
$data = [
    'messages' => [
        [
            'to' => 'recipient-phone-number', // Replace with recipient phone
            'from' => 'YourCompany',
            'text' => 'Your message here',
        ]
    ]
];

// Initialize cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $apiKey,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);
curl_close($ch);

// Return the response from Infobip API back to the frontend
echo $response;
?>
