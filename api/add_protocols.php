<?php
header('Content-Type: application/json');
require 'connection.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response['data'] = null;
    $response['message'] = "";

    // Retrieve and decode JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['email'])) {
        // Sanitize the email input
        $email = $conn->real_escape_string(trim($input['email']));

        // Insert new user into the protocol_subs table
        $sql = "INSERT INTO protocol_subs (user_email) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            // Retrieve the newly created user ID
            $userId = $stmt->insert_id;

            $response['status'] = true;
            $response['data'] = array(
                'id' => $userId,
                'email' => $email
            );
        } else {
            $response['status'] = false;
            $response['message'] = 'Registration failed: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['status'] = false;
        $response['message'] = 'Invalid input: email is required';
    }
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
