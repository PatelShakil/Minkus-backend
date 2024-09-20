<?php
// Allow from any origin
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");  // Specify your React dev server's URL
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require 'connection.php';

// Initialize response array
$response = array();
$response['data'] = null;
$response['message'] = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Query to fetch all users_tasks with corresponding user details using a JOIN
    $sql = "SELECT ut.*, u.* 
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
                echo $row;
                $task = array(
                    "task" => array(
                        "id" => $row['id'],  // assuming task ID
                        "title" => $row['title'],
                        "description" => $row['description'],
                        "date" => $row['date'],
                        // other task fields from users_tasks
                    ),
                    "user" => array(
                        "id" => $row['id'],  // assuming user ID
                        "name" => $row['name'],
                        "email" => $row['email'],
                        // other user fields from users
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
