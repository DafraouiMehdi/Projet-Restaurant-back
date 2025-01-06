<?php

try {
    $host = "localhost";
    $dbname = "pjres";
    $username = "root";
    $pwd = "";

    $connect = new PDO("mysql:host=$host;dbname=$dbname", $username, $pwd);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Connection Error: " . $e->getMessage();
    exit; 
}