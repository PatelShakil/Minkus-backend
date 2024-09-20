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

    // Convert date string to DateTime object
    $dateObject = DateTime::createFromFormat("Y-m-d", $date);

    // Convert DateTime object to string in 'Y-m-d' format for database insertion
    $formattedDate = $dateObject->format('Y-m-d');

    $title = $conn->real_escape_string(trim($_POST['title']));
    $desc = $conn->real_escape_string(trim($_POST['desc']));

    // Check if the email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $response['status'] = false;
        $response['message'] = 'User does not exist';
    } else {
        // Insert the task
        $sql = "INSERT INTO users_tasks (title, description, user_email, date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind the date as a string
        $stmt->bind_param("ssss", $title, $desc, $email, $formattedDate);

        if ($stmt->execute()) {
            $response['status'] = true;
            $response['message'] = "Task created successfully";
        } else {
            $response['status'] = false;
            $response['message'] = 'Task creation failed: ' . $stmt->error;
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
