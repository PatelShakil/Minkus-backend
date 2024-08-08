<?php
header('Content-Type: application/json');
require 'connection.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Check if the email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['status'] = false;
        $response['message'] = 'Email already registered';
    } else {
        // Hash the password
        

        // Insert new user
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            // Retrieve the newly created user
            $userId = $stmt->insert_id;
            $stmt->close();

            // Get user details
            $userQuery = "SELECT id, name, email FROM users WHERE id = ?";
            $stmt = $conn->prepare($userQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($id, $name, $email);
            $stmt->fetch();

            $response['status'] =true;
            $response['user'] = array(
                'id' => $id,
                'name' => $name,
                'email' => $email
            );
        } else {
            $response['status'] = false;
            $response['message'] = 'Registration failed: ' . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
