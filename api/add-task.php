<?php
// Allow from any origin
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");  // Specify your React dev server's URL
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require 'connection.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response['data'] = null;
    $response['message'] = "";
    // Retrieve and sanitize input
    $email = $conn->real_escape_string(trim($_POST['email']));
    $date = $_POST['date'];
    $title = $conn->real_escape_string(trim($_POST['title']));
    $desc = $conn->real_escape_string(trim($_POST['desc']));

    // Check if the email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows < 0) {
        $response['status'] = false;
        $response['message'] = 'User not exists';
    } else {
        // Hash the password


        // Insert new user
        $sql = "INSERT INTO users_tasks ( title,description,user_email,date) VALUES (?,?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssd", $title, $desc, $email, $date);

        if ($stmt->execute()) {
            // Retrieve the newly created user

            $response['status'] = true;
            $response['message'] = "Task created successfully";
        } else {
            $response['status'] = false;
            $response['message'] = 'Task Creation failed: ' . $stmt->error;
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
