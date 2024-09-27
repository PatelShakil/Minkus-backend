<?php
// Allow from any origin
header('Content-Type: application/json');
$allowed_origins = ['http://localhost:5173', 'https://admin.app-minkus.com'];
if (in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require 'connection.php';

// Initialize response array
$response = array();
$response['data'] = null;
$response['message'] = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Query to fetch all users
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();  // Get the result set from the prepared statement

        if ($result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);  // Fetch all users as an associative array

            $response['status'] = true;
            $response['data'] = $data;
            $response['message'] = 'Users found';
        } else {
            $response['status'] = false;
            $response['message'] = 'No users found';
        }

        $stmt->close();
    } else {
        $response['status'] = false;
        $response['message'] = 'Failed to prepare the statement';
    }
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
