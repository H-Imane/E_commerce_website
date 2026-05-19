<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "db_ecommerce";
$port = 3306;

$dsn = "mysql:host=" . $host . ";dbname=" . $dbname . ";port=" . $port;

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// Throw exceptions on database errors
} catch (Exception $e) {
    error_log('DB connection error: ' . $e->getMessage());
    // if this file is included by an API endpoint -> return a JSON error and stop
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(["success" => false, "error" => "Database connection error"]);
    exit;
}
?>
