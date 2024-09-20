<?php
// Allow from any origin
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");  // Specify your React dev server's URL
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require 'connection.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response['data'] = null;
    $response['message'] = "";

    // Query to check if user exists
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($data);
        $stmt->fetch();
        if ($data != null) {
            $response['data'] = $data;
            $response['message'] = 'Users Found';
        }
    } else {
        $response['status'] = false;
        $response['message'] = 'no users found';
    }

    $stmt->close();
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
