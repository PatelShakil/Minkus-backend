<?php

// Allow from any origin
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");  // Specify your React dev server's URL
header("Access-Control-Allow-Origin: https://admin.app-minkus.com");  // Specify your React dev server's URL

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
require('connection.php'); // Assuming this file contains the $conn variable for database connection

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Initialize response array
        $response = [
            'status' => false,
            'message' => '',
            'data' => null
        ];

        // Retrieve user_email from GET parameters
        if (isset($_POST['id'])) {
            $id = mysqli_real_escape_string($conn, $_POST['id']);

            $sql = "DELETE FROM users_tasks WHERE id = '$id'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                // Construct the response data
                $response['status'] = true;
                $response['message'] = 'Tasks Deleted successfully';
                $response['data'] = $tasks;
            } else {
                $response['message'] = 'No tasks found.';
            }
        } else {
            $response['message'] = 'Task ID is required.';
        }
    } catch (Exception $e) {
        // If an error occurred during the query execution, log it
        error_log($e->getMessage());
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }

    // Send JSON response back to the client
    echo json_encode($response);
} else {
    // If the request method is not GET, return an error
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
