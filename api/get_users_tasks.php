<?php

header('Content-Type: application/json');
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
        if (isset($_POST['user_email'])) {
            $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);

            $sql = "SELECT * FROM users_tasks WHERE user_email = '$user_email'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $tasks = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $tasks[] = $row;
                }

                // Construct the response data
                $response['status'] = true;
                $response['message'] = 'Tasks retrieved successfully';
                $response['data'] = $tasks;
            } else {
                $response['message'] = 'No tasks found for the specified user.';
            }
        } else {
            $response['message'] = 'User email is required.';
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
