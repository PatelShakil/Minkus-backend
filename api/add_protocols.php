<?php
header('Content-Type: application/json');
require 'connection.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response['data'] = null;
    $response['message'] = "";
    // Retrieve and sanitize input
    $email = $conn->real_escape_string(trim($_POST['email']));

        // Insert new user
        $sql = "INSERT INTO protocol_subs (user_email) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s",$email);

        if ($stmt->execute()) {
            // Retrieve the newly created user
            $userId = $stmt->insert_id;
            $stmt->close();


            $response['status'] = true;
            $response['data'] = array(
                'id' => $userId,
            );
        } else {
            $response['status'] = false;
            $response['message'] = 'Registration failed: ' . $stmt->error;
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
