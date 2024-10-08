<?php
// Allow from any origin
header('Content-Type: application/json');
$allowed_origins = ['http://localhost:5173', 'https://admin.app-minkus.com'];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

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

    // Check if the date format is valid
    if (!DateTime::createFromFormat('Y-m-d', $date)) {
        $response['status'] = false;
        $response['message'] = 'Invalid date format. Use Y-m-d format.';
        echo json_encode($response);
        exit;
    }

    // Convert date string to DateTime object and format it
    $dateObject = DateTime::createFromFormat("Y-m-d", $date);
    $formattedDate = $dateObject->format('Y-m-d');  // Format it back to Y-m-d

    $title = $conn->real_escape_string(trim($_POST['title']));
    $desc = $conn->real_escape_string(trim($_POST['desc']));
    $is_mull = isset($_POST['is_mull']) ? intval($_POST['is_mull']) : 0; // Assuming it's an integer (1 or 0)
    $color = $conn->real_escape_string(trim($_POST['color']));

    // Check if the user email exists in the database
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $response['status'] = false;
        $response['message'] = 'User does not exist';
    } else {
        // Insert new task into users_tasks table with is_mull and color fields
        $sql = "INSERT INTO users_tasks (title, description, user_email, date, is_mull, color) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind date as a string
        $stmt->bind_param("ssssss", $title, $desc, $email, $formattedDate, $is_mull, $color);

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
