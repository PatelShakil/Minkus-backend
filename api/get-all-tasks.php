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
$response['data'] = null;
$response['message'] = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Query to fetch all users_tasks with corresponding user details using a JOIN, with column aliases
    $sql = "SELECT 
                ut.id AS task_id, 
                ut.title AS task_title, 
                ut.description AS task_description, 
                ut.date AS task_date, 
                ut.is_mull, 
                ut.color,
                u.id AS user_id, 
                u.name AS user_name, 
                u.email AS user_email
            FROM users_tasks ut
            JOIN users u ON ut.user_email = u.email";  // JOIN users based on the matching email

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();  // Get the result set from the prepared statement

        if ($result->num_rows > 0) {
            $tasks = array();  // Initialize an array to hold task details along with user info

            // Fetch each row from the result set
            while ($row = $result->fetch_assoc()) {
                $task = array(
                    "task" => array(
                        "id" => $row['task_id'],
                        "title" => $row['task_title'],
                        "description" => $row['task_description'],
                        "date" => $row['task_date'],
                        "is_mull" => $row['is_mull'],  // Ensure these columns exist in your DB
                        "color" => $row['color']
                    ),
                    "user" => array(
                        "id" => $row['user_id'],
                        "name" => $row['user_name'],
                        "email" => $row['user_email'],
                    )
                );

                // Add the task-user pair to the tasks array
                array_push($tasks, $task);
            }

            // Set the response data
            $response['status'] = true;
            $response['data'] = $tasks;
            $response['message'] = 'Tasks with users found';
        } else {
            $response['status'] = false;
            $response['message'] = 'No tasks found';
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
