<?php
header('Content-Type: application/json; charset=utf-8');// force json responses

// database connection and session handling
require "../../connection/db.php";
require "../../session/session.php";

// read json request body
$data = json_decode(file_get_contents("php://input"), true);

// validate required fields
if (
    empty($data['email']) ||
    empty($data['password'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

$email = $data['email'];
$password = $data['password'];

// fetch user by email
try {
    $sql = "SELECT id, name, email, hashed_password, role
            FROM users
            WHERE email = :email
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Server error"
    ]);
    exit;
}
// invalid email or password
if (!$user || !password_verify($password, $user['hashed_password'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Invalid credentials"]);
    exit;
}

 // store user info in session (NO ROLE - security)
$_SESSION['user'] = [
    "id" => $user['id'],
    "name" => $user['name'],
    "email" => $user['email']
];

// successful login response (include role for frontend redirect only)
echo json_encode([
    "success" => true,
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "role" => $user['role']  // only for initial redirect
    ]
]);
