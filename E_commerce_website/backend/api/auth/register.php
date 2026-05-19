<?php
// return json responses
header('Content-Type: application/json; charset=utf-8');

// database connection and session handling
require "../../connection/db.php";
require "../../session/session.php";

// read json request body
$data = json_decode(file_get_contents("php://input"), true);

// check required fields
if (
    !isset($data['name']) ||
    !isset($data['email']) ||
    !isset($data['password'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // validate email format
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid email"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);// hash the password 

// insert new user into database
try {
    $sql = "INSERT INTO users (name, email, hashed_password)
            VALUES (:name, :email, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        "name" => $name,
        "email" => $email,
        "password" => $hashedPassword
    ]);
    // registration successful
    echo json_encode(["success" => true]);

} catch (Exception $e) {
     // email already exists or constraint error
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "error" => "Email already exists"
    ]);
}
