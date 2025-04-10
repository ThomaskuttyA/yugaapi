<?php
include 'db_connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$sql = "SELECT * FROM yuga_users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$response = [];

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $response['status'] = 'success';
        $response['message'] = 'Login successful';
        $response['user'] = [
            'yugaid' => $user['yugaid'],
            'fullname' => $user['fullname'],
            'email' => $user['email'],
            'usertype' => $user['usertype']
        ];
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid password';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'User not found';
}

echo json_encode($response);
$conn->close();
?>
