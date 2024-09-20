<?php
// Allow from any origin
header("Access-Control-Allow-Origin: http://localhost:5173");  // Specify your React dev server's URL
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');
require 'connection.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response['data'] = null;
    $response['message'] = "";
    // Retrieve and sanitize input
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Query to check if user exists
    $sql = "SELECT id, name, password FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $db_password);
        $stmt->fetch();

        // Verify password
        if ($password == $db_password) {
            $response['status'] = true;
            $response['data'] = array(
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'password' => $password
            );
        } else {
            $response['status'] = false;
            $response['message'] = 'Invalid email or password';
        }
    } else {
        $response['status'] = false;
        $response['message'] = 'Invalid email or password';
    }

    $stmt->close();
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
return json_encode($response);
