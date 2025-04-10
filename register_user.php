<?php
// === CORS HEADERS ===
header("Access-Control-Allow-Origin: *"); // Or replace * with http://localhost:4200 for security
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === Include DB Connection ===
include 'db_connection.php';

// === Get Data from Request ===
$data = json_decode(file_get_contents("php://input"), true);

$fullname = $data['fullname'] ?? '';
$email = $data['email'] ?? '';
$mobile = $data['mobile'] ?? '';
$password = $data['password'] ?? '';
$usertype = $data['usertype'] ?? 'client'; // default value

// === Generate unique YugaID ===
$yugaid = 'YUGA' . time(); // You can make this more unique with random or UUID

// === Hash the password ===
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// === Insert into DB ===
$sql = "INSERT INTO yuga_users (yugaid, fullname, email, mobilenumber, password, usertype)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $yugaid, $fullname, $email, $mobile, $hashedPassword, $usertype);

$response = [];

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'User registered successfully.';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Registration failed: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// === Return JSON Response ===
header('Content-Type: application/json');
echo json_encode($response);
?>
